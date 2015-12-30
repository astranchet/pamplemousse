<?php
namespace Pamplemousse\Photos;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class Router implements ControllerProviderInterface
{
    /**
     * Routes configuration
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        # TODO : check filename and dimensions
        $controllers->get('/thumbnail/{filename}/{width}x{height}', Controller::class . "::thumbnailAction")
            ->bind('thumbnail');

        return $controllers;
    }
}