<?php

defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\System\Info;
use Concrete\Core\API\Transformer\InfoTransformer;
use League\Fractal\Resource\Item;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Foundation\Bus\Command\CreatePageCommand;

/**
 * @var $router \Concrete\Core\Routing\Router
 */
$router->get('/system/info', function() {
    $info = new Info();
    return new Item($info, new InfoTransformer());
});

$router->post('page/create', function () {
    $app = Facade::getFacadeApplication();
    $commandBus = $app->make('bus');
    return $commandBus->handle(new CreatePageCommand());
});
