<?php
namespace Concrete\Core\Foundation\Bus\Command;


interface CommandInterface
{

    /**
     * @return mixed
     */
    public function execute() ;

    /**
     * @return boolean
     */
    public function isApiRequest();


    /**
     * @param array $data
     */
    public function setOptions($options = []);

    /**
     * @param array $data
     */
    public function setData($data = []);

}
