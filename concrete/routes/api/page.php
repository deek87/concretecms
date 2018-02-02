<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $router \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */
$commandBus = $app->make('bus');
$router->post('page/create', function () use ($app, $commandBus) {
    /** @var $command \Concrete\Core\Foundation\Bus\Command\CreatePageCommand */
    $command = $app->make(\Concrete\Core\Foundation\Bus\Command\Page\CreatePageCommand::class);
    $command->setIsApiRequest(true);
    $commandBus->handle($command);
    return $command->getReturnObject();
});

$router->get('page/{id}/info', function ($id) use ($app, $commandBus) {
    $page = \Concrete\Core\Page\Page::getByID($id);
    return $page;
}, '\d+');

$router->get('page/list', function () use ($app, $commandBus) {
    /** @var $command \Concrete\Core\Foundation\Bus\Command\CreatePageCommand */
    $command = $app->make(\Concrete\Core\Foundation\Bus\Command\Page\FilterPageListCommand::class);
    $command->setIsApiRequest(true);
    $commandBus->handle($command);
    return $command->getReturnObject();
});