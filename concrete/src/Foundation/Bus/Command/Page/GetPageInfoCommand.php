<?php


namespace Concrete\Core\Foundation\Bus\Command\Page;


use Concrete\Core\Foundation\Bus\Command\AbstractCommand;
use Concrete\Core\Page\Page;

class GetPageInfoCommand extends AbstractCommand
{

    /** @var Page|null $page  */
    protected $page;

    public function __construct($pageID)
    {
        parent::__construct();
        $this->page = Page::getByID($pageID);
    }

    public function setPage(Page $page) {
        $this->page = $page;
    }

    public function execute()
    {
        if ($this->page instanceof Page) {
            return $this->page;
        } else {
            throw new \Exception(t('Invalid Page Given'));
        }

    }
}