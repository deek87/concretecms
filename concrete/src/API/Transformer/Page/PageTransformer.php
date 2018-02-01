<?php

namespace Concrete\Core\API\Transformer\Page;

use Concrete\Core\Page\Page;
use League\Fractal\TransformerAbstract;

class PageTransformer extends TransformerAbstract
{

    /**
     * Transform a Page object into an array for API requests
     *
     * @param Page $page
     * @return array
     */
    public function transform(Page $page)
    {
        $parent = Page::getByID($page->getCollectionParentID());
        $pageArray = [
            'page_id'=>$page->getCollectionID(),
            'page_name'=>$page->getCollectionName(),
            'page_handle'=>$page->getCollectionHandle(),
            'page_path'=>$page->getCollectionPath(),
            'page_description'=>$page->getCollectionDescription(),
            'display_date'=>$page->getCollectionDatePublic(),
            'page_type'=>$page->getCollectionTypeHandle(),
            'page_template'=>$page->getPageTemplateHandle(),
            'page_theme'=>$page->getCollectionThemeObject()->getThemeDisplayName(),
        ];

        if (is_object($parent) && !$parent->isError()) {
            $pageArray['parent_page'] = [
                'page_id'=>$parent->getCollectionID(),
                'page_name'=> $parent->getCollectionName(),
                'page_handle'=>$parent->getCollectionHandle(),
                'page_path'=>$parent->getCollectionPath(),
            ];
        }

        return $pageArray;
    }

}
