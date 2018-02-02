<?php

namespace Concrete\Core\Foundation\Bus\Handler;


use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Bus\Command\AbstractCommand;
use Concrete\Core\API\Transformer\BasicTransformer;
use Concrete\Core\Foundation\Bus\Command\CommandInterface;
use League\Fractal\Resource\Item;
use Concrete\Core\Http\Request;

/**
 * The default handler for all commands without one
 *
 * Class DefaultCommandHandler
 * @package Concrete\Core\Foundation\Bus\Handler
 */
class DefaultCommandHandler extends AbstractCommandHandler
{
    /** @var  AbstractCommand */
    protected $command;
    /** @var Application $app */
    protected $app;
    /** @var  Request $request */
    protected $request;

    /**
     * @param CommandInterface $command
     * @return mixed
     */
    public function handle(CommandInterface $command) {

        $this->command = $command;
        if ($command->isApiRequest()) {
            $this->getRequestData();
            $results = $command->getReturnObject();

            return new Item($results, new BasicTransformer());
        } else {
            $results = $command->getReturnObject();
            return $results;
        }
    }
}