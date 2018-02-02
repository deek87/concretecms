<?php


namespace Concrete\Core\Foundation\Bus\Handler\Page;

use Concrete\Core\Foundation\Bus\Command\Page\FilterPageListCommand;
use Concrete\Core\Foundation\Bus\Handler\AbstractCommandHandler;
use Concrete\Core\Validation\SanitizeService;
use Concrete\Core\Page\Page;

class FilterPageListCommandHandler extends AbstractCommandHandler
{
    /** @var $command FilterPageListCommand */
    protected $command;

    public function handle(FilterPageListCommand $command)
    {

       if ($command->isApiRequest()) {
           // Do API Specific things
           $this->getRequestData();
       } else {
        // Do Non-API Specific Things
       }
       // Do things for every one
    }


    protected function parseOptions()
    {
        if ($this->command->getOption('page')) {
            $this->command->setOption('parentPage', $this->command->getOption('page'));
        }
        if ($this->command->getOption('parent')) {
            $this->command->setOption('parentPage', $this->command->getOption('parent'));
        }

        if (is_array($this->command->getOption('filters'))) {
            foreach ($this->command->getOption('filters') as $filter) {
                $this->determineFilters($filter);
            }
        }



    }

    /**
     * Function used to determine which filters to use
     *
     * @param $filter
     */
    protected function determineFilters($filter)
    {
        switch ($filter){
            case ('keywords' || 'words' || 'text'):
                $this->command->getPageList()->filterByKeywords($this->command->getOption('keywords'));
                break;
            default;
            case ('parent' || 'page' || 'parentPage'):
                $this->getParentPage($this->command->getOption('parentPage'));
                break;
            case ('block' || 'blockType'):
                $this->command->getPageList()->filterByBlockType($this->command->getOption('blockType'));
                break;
        }
    }


    protected function getParentPage($string) {

        if ($string instanceof Page) {
            $this->command->getPageList()->filterByParentID($this->command->getOption('parentPage')->getCollectionID());
        } else {
            /** @var SanitizeService $sanitizer */
            $sanitizer = $this->app->make(SanitizeService::class);
            $page = Page::getByID($sanitizer->sanitizeInt($string));
            if (is_object($page) && !$page->isError()) {
                $this->command->getPageList()->filterByParentID($page->getCollectionID());
            }

            $page = Page::getByPath($sanitizer->sanitizeString($string));
            if (is_object($page) && !$page->isError()) {
                $this->command->getPageList()->filterByParentID($page->getCollectionID());
            }
        }
    }


}