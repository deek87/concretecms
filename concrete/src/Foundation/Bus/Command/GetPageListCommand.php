<?php


namespace Concrete\Core\API\Commands;


use Concrete\Core\API\Transformer\PageListTransformer;
use Concrete\Core\Page\PageList;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;
use League\Fractal\Resource\Item;

class GetPageListCommand extends AbstractCommand
{
    /** @var  PageList $pageList */
    protected $pageList;

    public function getDataFromRequest()
    {
        $this->data = $this->parseData([
            'username'=>$this->request->get('user_name'),
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
        $this->pageList = new PageList();
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
            case 'keywords':
                $this->pageList->filterByKeywords($this->request->get('keywords'));
                break;
                default;

        }

    }

}