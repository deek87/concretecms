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
    /** @var array $data */
    protected $data = [];
    /** @var  array $options */
    protected $options = [];
    /** @var boolean $isApiRequest */
    protected $isApiRequest = false;
    /** @var mixed $returnObject */
    protected $returnObject = null;

    /**
     * Determines whether or not this command is an api request
     *
     * @return bool
     */
    public function isApiRequest()
    {
        return $this->isApiRequest;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param bool $isApiRequest
     */
    public function setIsApiRequest(bool $isApiRequest)
    {
        $this->isApiRequest = $isApiRequest;
    }


    /**
     * Function used to return objects
     * Eg. PageList from GetPageListCommand
     *
     * @return mixed
     */
    public function getReturnObject()
    {
        return $this->returnObject;
    }

    /**
     * @param $returnObject mixed
     */
    public function setReturnObject($returnObject = null) {
        $this->returnObject = $returnObject;
    }


    /**
     * Function for manually setting data
     *
     * @param array $data
     */
    public function setData($data = [])
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Function used to get one option
     *
     * @param $option
     * @return mixed
     */
    public function getOption($option)
    {
        return $this->options[$option];
    }

    /**
     *  Function used to set various options
     *
     * @param array $options
     */
    public function setOptions($options=[])
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Function used to reset all of the options on the command
     */
    public function resetOptions() {
        $this->options = [];
    }

    /**
     * @param $option
     * @param string $value
     */
    public function setOption($option, $value = '') {
        $this->options[$option] = $value;
    }





}