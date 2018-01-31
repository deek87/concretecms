<?php
namespace Concrete\Core\Foundation\Bus\Command;

interface CommandInterface
{

    /**
     * @return Item
     */
    public function execute() ;


    /**
     * @param array $data
     */
    public function setOptions($options = []);

    /**
     * @param array $data
     */
    public function setData($data = []);

}
