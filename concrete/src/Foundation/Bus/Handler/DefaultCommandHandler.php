<?php

namespace Concrete\Core\Foundation\Bus\Handler;


use Concrete\Core\Foundation\Bus\Command\AbstractCommand;
use Concrete\Core\API\Transformer\BasicTransformer;
use League\Fractal\Resource\Item;

/**
 * The default handler for all commands without one
 *
 * Class DefaultCommandHandler
 * @package Concrete\Core\Foundation\Bus\Handler
 */
class DefaultCommandHandler
{

    /**
     * @param $command  AbstractCommand
     * @return Item
     */
    public function handle($command) {

        $results = $command->execute();
        if ($command->isApiRequest()) {
            return new Item($results, new BasicTransformer());
        } else {
            return $results;
        }
    }
}