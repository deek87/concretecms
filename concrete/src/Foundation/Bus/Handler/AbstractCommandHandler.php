<?php

namespace Concrete\Core\Foundation\Bus\Handler;


use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Bus\Command\AbstractCommand;
use Concrete\Core\Http\Request;

/**
 * The default handler for all commands without one
 *
 * Class AbstractCommandHandler
 * @package Concrete\Core\Foundation\Bus\Handler
 */
abstract class AbstractCommandHandler
{
    /** @var  AbstractCommand */
    protected $command;
    /** @var Application $app */
    protected $app;
    /** @var  Request $request */
    protected $request;

    /**
     * AbstractCommandHandler constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->request = Request::getInstance();
    }


    /**
     * Function that gets select data or all the data from a request
     * @param AbstractCommand $command
     */
    protected function getRequestData() {
        if ($this->request->getRealMethod() === 'GET') {
            // Return all of the Body Paramaters
            $this->parseRequestData($options = $this->request->query->all());
        } else {
            // Return all of the Body Paramaters
            $this->parseRequestData($this->request->request->all());
        }
    }

    /**
     * Parses the request data and sets the options/data
     *
     * @param array $options
     */
    protected function parseRequestData($options = []) {
        if (isset($options['data'])) {
            $this->command->setData($options['data']);
        } else {
            $this->command->setData([]);
        }

        $this->command->setOptions($options);
    }


}