<?php

namespace Concrete\Core\Foundation\Bus\Handler\Page;


use Concrete\Core\Foundation\Bus\Command\AbstractCommand;
use Concrete\Core\API\Transformer\Page\PageTransformer;
use League\Fractal\Resource\Item;

class GetPageInfoCommandHandler
{
    /**
     * @param $command  AbstractCommand
     * @return Item
     */
    public function handle($command) {

        $page = $command->execute();
        if ($command->isApiRequest()) {
            return new Item($page, new PageTransformer());
        } else {
            return $page;
        }
    }
}