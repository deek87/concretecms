<?php
namespace Concrete\Core\API;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Routing\Router;
class APIServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('api', function ($app) {
            return $app->make('Concrete\Core\API\ClientFactory');
        });
    }

}