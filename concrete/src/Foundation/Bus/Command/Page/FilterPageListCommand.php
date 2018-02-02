<?php


namespace Concrete\Core\Foundation\Bus\Command\Page;


use Concrete\Core\API\Transformer\PageListTransformer;
use Concrete\Core\Page\PageList;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Page\Page;
use Concrete\Core\Validation\SanitizeService;
use League\Fractal\Resource\Item;
use Concrete\Core\Foundation\Bus\Command\AbstractCommand;

class FilterPageListCommand extends AbstractCommand
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

    /**
     * @return PageList
     */
    public function getPageList()
    {
        return $this->pageList;
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

    public function getReturnObject()
    {
        return $this->pageList;
    }



}