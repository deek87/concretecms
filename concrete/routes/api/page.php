<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $router \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */
$commandBus = $app->make('bus');
$router->post('page/create', function () use ($app, $commandBus) {

    $command = $app->make(\Concrete\Core\Foundation\Bus\Command\CreatePageCommand::class);
    $command->setIsApiRequest(true);
    return $commandBus->handle($command);
});

$router->get('page/{id}/info', function ($id) use ($app, $commandBus) {

    $command = $app->make(\Concrete\Core\Foundation\Bus\Command\Page\GetPageInfoCommand::class, [$id]);
    $command->setIsApiRequest(true);
    return $commandBus->handle($command);
}, '\d+');