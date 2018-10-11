<?php
namespace Concrete\Core\Express\Association;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Concrete\Core\Express\EntryList;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class Applier
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * A generic associate method that can be run from elsewhere. Determines the appropriate associate* method
     * to run.
     * @param Association $association
     * @param Entry $entry
     * @param $input
     */
    public function associate(Association $association, Entry $entry, $input)
    {
        if ($association instanceof ManyToOneAssociation) {
            return $this->associateManyToOne($association, $entry, $input);
        }
        if ($association instanceof OneToOneAssociation) {
            return $this->associateOneToOne($association, $entry, $input);
        }
        if ($association instanceof OneToManyAssociation) {
            return $this->associateOneToMany($association, $entry, $input);
        }
        if ($association instanceof ManyToManyAssociation) {
            return $this->associateManyToMany($association, $entry, $input);
        }
    }


    /**
     * Function used to associate Objects in a Many To One relationship
     *
     * @param Association $association
     * @param Entry $entry
     * @param Entry $associatedEntry
     *
     *
     * example of use:
     * $sourceEntity is the Many
     * $targetEntity is the One
     * $applier->associateManyToOne($manyToOneAssociation, $sourceEntity, $targetEntry);
     */
    public function associateManyToOne(Association $association, Entry $entry, Entry $associatedEntry)
    {
        // Check if this entry has a OneAssociation (if it doesnt create a new one)
        $oneAssociation = $entry->getEntryAssociation($association);
        if (!is_object($oneAssociation)) {
            $oneAssociation = new Entry\OneAssociation();
            $oneAssociation->setAssociation($association);
            $oneAssociation->setEntry($entry);
        } else {
            // If it exists check if this is already associated to the associated entry
            $selectedEntry = $oneAssociation->getSelectedEntries()->get(0);
            if (is_object($selectedEntry) && $selectedEntry->getID() !== $associatedEntry->getID()) {

                $oneAssociation->getSelectedEntriesCollection()->remove($selectedEntry);
                $this->entityManager->persist($oneAssociation);
                $oneAssociation->setSelectedEntries(new ArrayCollection());

                // Get the inveresed Association on selectedEntry and remove this entry from it
                $inversedAssociation = $this->getInverseAssociation($association);
                $manyAssociation = $selectedEntry->getEntryAssociation($inversedAssociation);
                if ($manyAssociation) {
                    $collection = $manyAssociation->getSelectedEntriesCollection();
                    if ($collection) {
                        $collection->removeElement($entry);
                        $manyAssociation->setSelectedEntries(new ArrayCollection($collection->getValues()));
                        $this->entityManager->persist($manyAssociation);

                    }
                }

            }
        }

        $associatedAssociationEntry = new Entry\AssociationEntry();
        $associatedAssociationEntry->setEntry($associatedEntry);
        $associatedAssociationEntry->setAssociation($oneAssociation);
        $this->entityManager->persist($associatedAssociationEntry);

        $oneAssociation->getSelectedEntriesCollection()->add($associatedAssociationEntry);
        $this->entityManager->persist($oneAssociation);
        if (!$oneAssociation->getSelectedEntries()->contains($associatedEntry)) {
            $oneAssociation->getSelectedEntries()->clear();
            $oneAssociation->getSelectedEntries()->add($associatedEntry);
            $this->entityManager->persist($oneAssociation);
        }

        // Get the Inversed Association (the many) or Create it
        $inversedAssociation = $this->getInverseAssociation($association);
        $manyAssociation = $associatedEntry->getEntryAssociation($inversedAssociation);
        if (!is_object($manyAssociation)) {
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($inversedAssociation);
            $manyAssociation->setEntry($associatedEntry);
            $this->entityManager->persist($associatedEntry);
            $this->entityManager->refresh($associatedEntry);
            $this->entityManager->persist($manyAssociation);
        }

        // Now get the Many Collection
        $collection = $manyAssociation->getSelectedEntriesCollection();
        if (!$collection) {
            $collection = new ArrayCollection();
        }
        $displayOrder = count($collection);

        $associationEntry = new Entry\AssociationEntry();
        $associationEntry->setEntry($entry);
        $associationEntry->setDisplayOrder($displayOrder);
        $associationEntry->setAssociation($manyAssociation);

        $collection->add($associationEntry);
            // Array Collection is (sometimes) required so doctrine doesnt do weird things and create new empty entries
            $manyAssociation->setSelectedEntries(new ArrayCollection($collection->getValues()));
            $this->entityManager->persist($entry);
            $this->entityManager->refresh($entry);
            $this->entityManager->persist($manyAssociation);

        // Save to Database
        $this->entityManager->flush();
    }

    /**
     *  Function used to associate Objects in a Many To One relationship
     *
     * @param Association $association
     * @param Entry $entry
     * @param array | ArrayCollection $associatedEntries
     *
     * example of use:
     * $sourceEntity is the One
     * $targetEntities is the Many (An array of entries, or ArrayCollection of Entries)
     * $applier->associateOneToMany($oneToManyAssociation, $sourceEntity, $targetEntities);
     *
     */
    public function associateOneToMany(Association $association, Entry $entry, $associatedEntries)
    {
        // Check if this entry has a ManyAssociation (if it doesnt create a new one)
        $manyAssociation = $entry->getEntryAssociation($association);
        if (!is_object($manyAssociation)) {
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($association);
            $manyAssociation->setEntry($entry);
        }
        // Let's clear all those old associatedEntries
        $collection = $manyAssociation->getSelectedEntriesCollection()->getValues();
        $inversedAssociation = $this->getInverseAssociation($association);
        if ($associatedEntries instanceof ArrayCollection) {
            $manyAssociation->setSelectedEntries($associatedEntries);
        } else {
            $manyAssociation->setSelectedEntries(new ArrayCollection($associatedEntries));
        }

        $this->entityManager->persist($manyAssociation);

        // Remove entries not apart of associatedEntries
        if (count($collection) > 0) {

            foreach ($collection as $currentEntry) {
                if (!$manyAssociation->getSelectedEntriesCollection()->contains($currentEntry)) {
                    $currentAssociation = $currentEntry->getEntryAssociation($inversedAssociation);
                    if (is_object($currentAssociation)) {
                        $currentAssociation->removeSelectedEntry($entry);
                        $this->entityManager->remove($currentAssociation); // Remove the OneAssociation
                    }
                }
            }

        }

        foreach($associatedEntries as $associatedEntry) {
            $oneAssociation = $associatedEntry->getEntryAssociation($inversedAssociation);
            if (!is_object($oneAssociation)) {
                $oneAssociation = new Entry\OneAssociation();
                $oneAssociation->setAssociation($inversedAssociation);
                $oneAssociation->setEntry($associatedEntry);
            }

            $collection = $oneAssociation->getSelectedEntriesCollection();

            // If the item appears in the request (meaning we want it to be selected):
            if (count($collection) == 0) {
                // Nothing is currently selected, so we have to add this one.
                $oneAssociation->setSelectedEntry($entry);
            } else {
                foreach($collection as $result) {
                    if ($result->getId() == $entry->getId()) {
                        // The result is already selected, so we don't reselect it.
                        continue;
                    } else {
                        // We are currently set to a different result. So we need to delete this association and
                        // Set it to this one.
                        $oneAssociation->clearSelectedEntry();
                        $oneAssociation->setSelectedEntry($entry);

                    }
                }
            }
        }
        $this->entityManager->persist($oneAssociation);
        $this->entityManager->flush();
    }

    public function associateManyToMany(Association $association, Entry $entry, $associatedEntries)
    {
        // Check if this entry has a ManyAssociation (if it doesnt create a new one)
        $manyAssociation = $entry->getEntryAssociation($association);
        if (!is_object($manyAssociation)) {
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($association);
            $manyAssociation->setEntry($entry);
            $displayOrder = 0;
        } else {
            $displayOrder = count($manyAssociation->getSelectedEntriesCollection());
        }
        $associatedAssociationEntries = [];
        foreach($associatedEntries as $associatedEntry) {
            $associatedAssociationEntry = new Entry\AssociationEntry();
            $associatedAssociationEntry->setEntry($associatedEntry);
            $associatedAssociationEntry->setAssociation($manyAssociation);
            $associatedAssociationEntry->setDisplayOrder($displayOrder);
            $displayOrder++;
            $associatedAssociationEntries[] = $associatedAssociationEntry;
        }

        foreach($manyAssociation->getSelectedEntriesCollection() as $manyAssociationSelectedEntry) {
            $this->entityManager->remove($manyAssociationSelectedEntry);
        }


        $manyAssociation->setSelectedEntries($associatedAssociationEntries);
        $this->entityManager->persist($manyAssociation);
        $inversedAssociation = $this->getInverseAssociation($association);

        foreach ($associatedEntries as $aEntry) {
            $otherManyAssociation = $aEntry->getEntryAssociation($inversedAssociation);
            if (!is_object($otherManyAssociation)) {
                $otherManyAssociation = new Entry\ManyAssociation();
                $otherManyAssociation->setAssociation($inversedAssociation);
                $otherManyAssociation->setEntry($aEntry);
                $otherManyAssociation->setSelectedEntries(new ArrayCollection([$entry]));

            } else {
                if (!$otherManyAssociation->getSelectedEntriesCollection()->contains($entry)) {
                    $selectedEntries = $manyAssociation->getSelectedEntries();
                    // If the item appears in the request (meaning we want it to be selected):
                    if (!$selectedEntries->contains($entry)) {
                        $associationEntry = new Entry\AssociationEntry();
                        $associationEntry->setAssociation($manyAssociation);
                        $associationEntry->setEntry($entry);
                        $associationEntry->setDisplayOrder(count($selectedEntries));
                        $otherManyAssociation->getSelectedEntriesCollection()->add($associationEntry);
                    }
                    $this->entityManager->persist($manyAssociation);
                }

            }
        }


        $this->entityManager->flush();
    }

    public function associateOneToOne(Association $association, Entry $entry, Entry $associatedEntry)
    {
        $oneAssociation = $entry->getEntryAssociation($association);
        if (!is_object($oneAssociation)) {
            $oneAssociation = new Entry\OneAssociation();
            $oneAssociation->setAssociation($association);
            $oneAssociation->setEntry($entry);
        } else {
            // We clear out the one association
            $selectedEntry = $oneAssociation->getSelectedEntries()->get(0);
            $oneAssociation->setSelectedEntries(new ArrayCollection());
            if ($selectedEntry) {
                // let's loop through the inverse association and remove this one from it.
                $inversedAssociation = $this->getInverseAssociation($association);
                $otherOneAssociation = $selectedEntry->getEntryAssociation($inversedAssociation);
                if ($otherOneAssociation) {
                    $otherOneAssociation->clearSelectedEntry();
                    $this->entityManager->persist($otherOneAssociation);
                }
            }
        }

        $oneAssociation->setSelectedEntry($associatedEntry);
        $this->entityManager->persist($oneAssociation);
        $this->entityManager->flush();

        $inversedAssociation = $this->getInverseAssociation($association);
        $oneAssociation = $associatedEntry->getEntryAssociation($inversedAssociation);

        if (!is_object($oneAssociation)) {
            $oneAssociation = new Entry\OneAssociation();
            $oneAssociation->setAssociation($inversedAssociation);
            $oneAssociation->setEntry($associatedEntry);
        } else {
            $selectedEntry = $oneAssociation->getSelectedEntries()->get(0);
            $oneAssociation->clearSelectedEntry();
            if ($selectedEntry) {

                $otherInversedAssociation = $this->getInverseAssociation($inversedAssociation);
                $otherOneAssociation = $selectedEntry->getEntryAssociation($otherInversedAssociation);
                if ($otherOneAssociation) {
                    $otherOneAssociation->clearSelectedEntry();
                    $this->entityManager->persist($otherOneAssociation);
                }
            }
        }

        $oneAssociation->setSelectedEntry($entry);
        $this->entityManager->persist($oneAssociation);
        $this->entityManager->getUnitOfWork()->removeFromIdentityMap($oneAssociation);
        $this->entityManager->flush();
        if (!empty($otherOneAssociation) && is_object($otherOneAssociation)) $this->entityManager->getUnitOfWork()->removeFromIdentityMap($oneAssociation);
    }

    public function removeAssociation(Association $association, Entry $entry)
    {
        $entryAssociation = $entry->getEntryAssociation($association);
        if ($entryAssociation) {
            $inversedAssociation = $this->getInverseAssociation($association);
            $associatedEntries = $entryAssociation->getSelectedEntriesCollection();
            foreach($associatedEntries as $associatedEntry) {
                $associatedEntryAssociation = $associatedEntry->getEntry()->getEntryAssociation($inversedAssociation);
                if (is_object($associatedEntryAssociation)) {
                    foreach($associatedEntryAssociation->getSelectedEntriesCollection() as $associatedAssociationEntry) {
                        if ($associatedAssociationEntry->getEntry()->getId() == $entry->getId()) {
                            $this->entityManager->remove($associatedAssociationEntry);
                        }
                    }
                }
                $this->entityManager->remove($associatedEntry);
            }

            $this->entityManager->flush();

            $this->entityManager->remove($entryAssociation);
            $this->entityManager->flush();

            if (isset($associatedEntryAssociation)) {
                $this->rescanAssociationDisplayOrder($associatedEntryAssociation);
            }
        }
    }

    private function rescanAssociationDisplayOrder(Entry\Association $association)
    {
        $displayOrder = 0;
        foreach($association->getSelectedEntriesCollection() as $selectedEntry) {
            $selectedEntry->setDisplayOrder($displayOrder);
            $this->entityManager->persist($selectedEntry);
            $displayOrder++;
        }
        $this->entityManager->flush();
    }

    protected function getInverseAssociation(Association $association)
    {
        return $this->entityManager->getRepository(Association::class)
            ->findOneBy([
                'target_property_name' => $association->getInversedByPropertyName(),
                'inversed_by_property_name' => $association->getTargetPropertyName(),
                'target_entity' => $association->getSourceEntity(),
                'source_entity' => $association->getTargetEntity()
            ]);
    }


}
