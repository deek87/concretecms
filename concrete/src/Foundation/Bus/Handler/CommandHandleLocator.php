<?php

namespace Concrete\Core\Foundation\Bus\Handler;


use League\Tactician\Handler\Locator\HandlerLocator;
use Concrete\Core\Http\Request;


class CommandHandleLocator implements HandlerLocator
{
    /**
     * @param string $commandName
     * @return null
     */
    public function getHandlerForCommand($commandName)
    {

        $commandName = end(explode('\\',$commandName));
        $request = Request::getInstance();
        $commandHandler = 'Concrete\Core\Foundation\Bus\Handler\\'.$commandName.'Handler';
        if (strpos($request->getPath(), 'ccm/api/v') != false ) {

            $apiHandler = 'Concrete\Core\Foundation\Bus\Handler\API\\'.$commandName.'Handler';
            if (class_exists($apiHandler)) {
                return new $apiHandler();
            }
        }
        if (class_exists($commandHandler)) {
                return new $commandHandler();
        }    else {
            return null;
        }

    }

}