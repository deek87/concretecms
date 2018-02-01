<?php


namespace Concrete\Core\API\Commands;


use Concrete\Core\API\Transformer\PageListTransformer;
use Concrete\Core\Page\PageList;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;
use Concrete\Core\Page\Page;
use Concrete\Core\Validation\SanitizeService;
use League\Fractal\Resource\Item;

class GetPageListCommand extends AbstractCommand
{
    /** @var  PageList $pageList */
    protected $pageList;

    public function __construct()
    {
        parent::__construct();
        $this->pageList = new PageList();
    }

    public function setOptions($options = [])
    {
        if ($options['parentPage'] instanceof Page) {
            $this->options['parentPage'] = $options['parentPage'];
        } else {
            $this->options['parentPage'] = $this->getPage($options);
        }

    }

    protected function getPage($string) {
        /** @var SanitizeService $sanitizer */
        $sanitizer = $this->app->make(SanitizeService::class);
        $page = Page::getByID($sanitizer->sanitizeInt($string));
        if (is_object($page) && !$page->isError()) {
            return $page;
        }

        $page = Page::getByPath($sanitizer->sanitizeString($string));
        if (is_object($page) && !$page->isError()) {
            return $page;
        }
        return null;
    }

    public function getDataFromRequest()
    {


        $this->data = $this->parseData([
            'author'=>$this->request->get('author'),
            'groupname'=>$this->request->get('group_name'),
            'groupid'=>$this->request->get('groupid'),
            ''
        ]);
    }

    public function execute()
    {


        $permissionChecker = new Checker();

        if ($permissionChecker->canSearchUser()) {
            return new Item($this->pageList, new PageListTransformer());
        } else {
            throw new \Exception(t('Access denied'));
        }


    }

    protected function parseData($data)
    {
        if (isset($this->data['filter'])) {
            if (!is_array($this->data['filter'])) {
                $this->data['filter'] = explode(',',$this->data['filter']);
            }
            foreach ($this->data['filter'] as $filter) {
                $this->filterPageList($filter);
            }
        }

     return $data;
    }

    /**
     * @param string|null $filter
     */
    protected function filterPageList($filter = null)
    {
        switch ($filter){
            case ('keywords' || 'words' || 'text'):
                $this->pageList->filterByKeywords($this->options['keywords']);
                break;
                default;
            case ('parent' || 'page' || 'parentPage'):
                $this->pageList->filterByParentID($this->options['parentID']);
                break;
            case ('block' || 'blockType'):
                $this->pageList->filterByBlockType($this->options['blockType']);
                break;


        }

    }

}