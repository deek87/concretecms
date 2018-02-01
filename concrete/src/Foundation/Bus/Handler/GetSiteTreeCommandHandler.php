<?php


namespace Concrete\Core\Foundation\Bus\Handler;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionTransformer;
use Concrete\Core\Foundation\Bus\Command\GetSiteTreeCommand;
use League\Fractal\Resource\Item;

class GetSiteTreeCommandHandler
{
    public function handle(GetSiteTreeCommand $command)
    {
        $collection = $command->execute();
       if ($command->isApiRequest()) {
           return new Item($collection, new TreeCollectionTransformer());
       } else {
           return $collection;
       }
    }

}