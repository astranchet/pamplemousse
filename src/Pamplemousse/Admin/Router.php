<?php
namespace Pamplemousse\Admin;

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

        $controllers->get('/', Controller::class . "::indexAction");

        $controllers->match('/edit', Controller::class . "::editAction")
            ->bind('edit-photos');

        $controllers->post('/file-upload', Controller::class . "::fileUploadAction")
            ->bind('file-upload');

        return $controllers;
    }
}
