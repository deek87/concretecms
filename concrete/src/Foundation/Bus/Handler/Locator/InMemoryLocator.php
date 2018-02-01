<?php

namespace Concrete\Core\Foundation\Bus\Handler\Locator;

use Concrete\Core\Foundation\Bus\Handler\DefaultCommandHandler;
use League\Tactician\Handler\Locator\InMemoryLocator as LeagueLocator;

class InMemoryLocator extends LeagueLocator
{
    /**
     * Returns the handler bound to the command's class name.
     *
     * @param string $commandName
     *
     * @return object
     */
    public function getHandlerForCommand($commandName)
    {

        preg_match('/([a-zA-Z0-9_\x7f-\xff]*)$/', $commandName, $matches);
        $commandName = $matches[0];

        if (!isset($this->handlers[$commandName])) {
            return new DefaultCommandHandler();
        }

        return $this->handlers[$commandName];
    }

}