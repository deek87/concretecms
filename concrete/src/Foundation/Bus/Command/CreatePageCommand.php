<?php


namespace Concrete\Core\Foundation\Bus\Command;


use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\User\User;
use Concrete\Core\Page\Type\Composer\Control\Control;
use Concrete\Core\Validation\SanitizeService;



class CreatePageCommand extends AbstractCommand
{

    /** @var \Concrete\Core\Entity\Page\Template $pageTemplate */
    protected $pageTemplate = null;
    /** @var \Concrete\Core\Page\Type\Type|null  $pageType */
    protected $pageType = null;
    /** @var Page|null $parent */
    protected $parent = null;
    /** @var string|null */
    protected $publishDate = null;

    /**
     * @param Type|null $pageType
     * @param Template|null $pageTemplate
     */
    protected function buildPageData (Type $pageType = null ,  Template $pageTemplate = null)
    {
        $this->data['uID'] = $this->app->make(User::class)->getUserID();
        if (!is_object($this->parent)) {
            $this->parent= $this->app->make('site')->getSite()->getSiteHomePageObject();
        }

        if (!is_object($this->parent) || $this->parent->isError()) {
            $this->parent = Page::getByID(HOME_CID);
        }

        if (is_object($pageType)) {
            $this->pageType = $pageType;
        } else {
            $this->pageType = $this->parent->getPageTypeObject();
            if (!is_object($this->pageType)) {
                $this->pageType = Type::getByHandle('page');
            }

        }
        if (is_object($pageTemplate)) {
            $this->pageTemplate = $pageTemplate;
        } else {
            if (!is_object($this->pageType)) {
                $this->pageTemplate = $this->parent->getPageTemplateObject();

            } else {
                $this->pageTemplate = $this->pageType->getPageTypeDefaultPageTemplateObject();
            }

        }

    }

    protected function getDataFromRequest()
    {
     $this->data = $this->parseData($this->request->request->get('data'));
    }

    protected function validateDate($publishDate) {
        if (is_null($publishDate) || empty($publishDate)) {
            return null;
        }

        $date = date_create($publishDate);
        if ($date) {
            return $date->format('Y-m-d H:i:s');
        } else {
            return null;
        }
    }

    protected function parseData($data)
    {
        /** @var SanitizeService $sanitizer */
        $sanitizer = $this->app->make(SanitizeService::class);


        $this->data = array_merge($data, ['cvIsApproved' => 0, 'cIsDraft' => 1, 'cIsActive' => false, 'cAcquireComposerOutputControls' => true]);
        $this->data['uID'] = $this->request->getCustomRequestUser()->getUserID();
        $this->data['cName'] = $this->request->get('name');
        $this->data['cDescription'] = $this->request->get('description');
        $this->data['cHandle'] = $sanitizer->sanitizeURL($this->request->get('url_slug'));
        $pageID = $this->request->get('parent');

        $this->parent = Page::getByID($pageID);
        if (!is_object($this->parent) || !$this->parent->isError()) {
            $this->parent = null;
        }

        $this->publishDate = $this->validateDate($this->request->get('publish_date'));


        $pageTypeID = $sanitizer->SanitizeInt($this->request->get('page_type'));
        $pageTypeHandle = $sanitizer->SanitizeString($this->request->get('page_type'));

        //Try ID first
        $pageType = Type::getByID($pageTypeID);
        if (!is_object($pageType)) {
            // If it fails then try the handle
            $pageType = Type::getByHandle($pageTypeHandle);
        }
        if (!is_object($pageType)) {
            // If both fail then set to null
            $pageType = null;
        }

        $pageTemplateID = $this->app->make(SanitizeService::class)->SanitizeInt($this->request->get('page_template'));
        $pageTemplateHandle = $this->app->make(SanitizeService::class)->SanitizeString($this->request->get('page_template'));

        //Try ID first
        $pageTemplate = Type::getByID($pageTemplateID);
        if (!is_object($pageTemplate)) {
            // If it fails then try the handle
            $pageTemplate = Type::getByHandle($pageTemplateHandle);
        }
        if (!is_object($pageTemplate)) {
            // If both fail then set to null
            $pageTemplate = null;
        }

        $this->buildPageData($pageType, $pageTemplate);

        return $this->data;
    }



    public function execute()
    {
        if (is_object($this->parent) && !$this->parent->isError()) {
        /** @var Page $pageDraft */
        $pageDraft = $this->parent->add($this->pageType, $this->data, $this->pageTemplate);
        } else {
            $pageDraft = null;
        }
        if (is_object($pageDraft) && !$pageDraft->isError()) {
            $pageDraft->setPageDraftTargetParentPageID($this->parent->getCollectionID());
            if (is_object($pageDraft->getPageTypeObject())) {
                $controlList = Control::getList($pageDraft->getPageTypeObject());
                foreach ($controlList as $control) {

                    $control->onPageDraftCreate($pageDraft);
                    $control->publishToPage($pageDraft, $this->data, $controlList);
                }



                $pageDraft->getPageTypeObject()->publish($pageDraft, $this->publishDate);
                // We need to get the most recent version as $pageDraft is currently the pageDraft version.
                $pageDraft = Page::getByID($pageDraft->getCollectionID(), 'RECENT');
                return $pageDraft;
            } else {
                return $pageDraft;
            }
        } else {
            return null;
        }

        return null;



    }

}