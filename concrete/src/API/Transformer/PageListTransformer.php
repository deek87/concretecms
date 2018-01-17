<?php
namespace Concrete\Core\API\Transformer;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\UserInfoRepository;
use League\Fractal\TransformerAbstract;
use Concrete\Core\Page\PageList;


class PageListTransformer extends TransformerAbstract
{

    public function transform(PageList $pageList)
    {
        //$app = Application::getFacadeApplication();
        //$userInfo = $app->make(UserInfoRepository::class);
        $pageArray = [];
        $results = $pageList->getResults();
        /** @var \Concrete\Core\Page\Page $page */
        foreach ($results as $page) {

            $pageArray[] = $page->getJSONObject();

            
        }

        return $pageArray;
    }

}
