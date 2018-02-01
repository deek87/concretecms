<?php

namespace Concrete\Core\Foundation\Bus\Command;

use Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider;


class GetSiteTreeCommand extends AbstractCommand
{

    public function execute()
    {
        $provider = $this->app->make(StandardSitemapProvider::class);
        $collection = $provider->getTreeCollection();
        return $collection;
    }


}