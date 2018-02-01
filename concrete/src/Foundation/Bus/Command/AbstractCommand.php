<?php

namespace Concrete\Core\Foundation\Bus\Command;


use Concrete\Core\API\Transformer\BasicTransformer;
use Concrete\Core\Application\Application;
use Concrete\Core\Http\Request;
use League\Fractal\Resource\Item;
use Concrete\Core\Support\Facade\Facade;

/**
 * Class AbstractCommand
 * This class is used for API commands to create/read/update/delete pages/users/blocks/etc
 */
abstract class AbstractCommand implements CommandInterface
{

    /** @var Application $app */
    protected $app;
    /** @var array $data */
    protected $data = [];
    /** @var  array $options */
    protected $options = [];
    /** @var  Request $request */
    protected $request;
    /** @var boolean $isApiRequest */
    protected $isApiRequest = false;

    /**
     * AbstractCommand constructor.
     */
    public function __construct()
    {
        $this->app = Facade::getFacadeApplication();
        $this->request = Request::getInstance();

    }


    /**
     * Determines whether or not this function is an api request
     *
     * @return bool
     */
    public function isApiRequest() {
        return $this->isApiRequest;
    }

    /**
     * @param bool $isApiRequest
     */
    public function setIsApiRequest(bool $isApiRequest)
    {
        $this->isApiRequest = $isApiRequest;
    }



    /**
     * Function that gets select data or all the data from a request
     */
    protected function getRequestData() {
        if ($this->request->getRealMethod() === 'GET') {
            // Return all of the Body Paramaters
            $this->data = $this->parseData($this->request->query->all());
        } else {
            // Return all of the Body Paramaters
            $this->data = $this->parseData($this->request->request->all());
        }
    }

    /**
     * Function for manually setting options
     *
     * @param array $options
     */
    public function setOptions($options = [])
    {
        $this->options = $this->parseOptions($options);
    }

    /**
     * Function for manually setting data
     *
     * @param array $data
     */
    public function setData($data = [])
    {
        $this->data = $this->parseData($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function parseData($data)
    {

        return $data;

    }

    /**
     * @param $options
     * @return mixed
     */
    protected function parseOptions($options)
    {
        return $options;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
   }


}