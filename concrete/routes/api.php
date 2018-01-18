<?php

defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\System\Info;
use Concrete\Core\API\Transformer\InfoTransformer;
use League\Fractal\Resource\Item;
use Concrete\Core\API\Commands\CreatePageCommand;

/**
 * @var $router \Concrete\Core\Routing\Router
 */
$router->get('/system/info', function() {
    $info = new Info();
    return new Item($info, new InfoTransformer());
});

$router->post('page/create', function () {
    $command = new CreatePageCommand();
    return $command->execute();
});
