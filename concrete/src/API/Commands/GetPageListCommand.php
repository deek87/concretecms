<?php


namespace Concrete\Core\Api\Commands;


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

        }


    }

    protected function parseData($data)
    {
        $pageList = new PageList();
        if (isset($this->data[''])) {

        }

     return $data;
    }

    /**
     * @param PageList $pageList
     */
    protected function filterPageList(&$pageList)
    {

    }

}