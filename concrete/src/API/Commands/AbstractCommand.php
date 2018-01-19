<?php

namespace Concrete\Core\API\Commands;


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
    protected $data;
    /** @var  array $options */
    protected $options;
    /** @var  Request $request */
    protected $request;

    /**
     * AbstractCommand constructor.
     */
    public function __construct()
    {
        $this->app = Facade::getFacadeApplication();
        $this->request = Request::getInstance();
        $this->getDataFromRequest();

    }


    /**
     * Function that gets select data or all the data from requests
     */
    protected function getDataFromRequest() {
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
     * This function is called by the API to return a fractal Item Object
     * @return Item
     */
    public function execute()
    {
        return new Item($this->data, new BasicTransformer());

    }


}