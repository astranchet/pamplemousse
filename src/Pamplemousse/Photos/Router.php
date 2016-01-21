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

        $controllers->get('/{photo}', Controller::class . "::photoAction")
            ->bind('photo')
            ->convert('photo', 'photos:getPhoto');

        $controllers->get('/thumbnail/{photo}/{width}x{height}', Controller::class . "::thumbnailAction")
            ->bind('thumbnail')
            ->convert('photo', 'photos:getPhoto')
            ->assert('width', '\d+')
            ->assert('height', '\d+')
            ;

        $controllers->get('/thumbnail/{photo}/{width}', Controller::class . "::thumbnailAction")
            ->bind('thumbnail-by-width')
            ->convert('photo', 'photos:getPhoto')
            ->assert('width', '\d+')
            ;

        return $controllers;
    }
}
