<?php

namespace Concrete\Core\API\Commands;

use Concrete\Core\Application\Application;
use League\Fractal\Resource\Item;

/**
 * Interface CommandInterface
 */
interface CommandInterface
{

    /**
     * @return Item
     */
    public function execute() ;

    /**
     * CommandInterface constructor.
     * @param Application $app
     */
    public function __construct(Application $app);

    /**
     * @param array $data
     */
    public function setOptions($options = []);

    /**
     * @param array $data
     */
    public function setData($data = []);





}