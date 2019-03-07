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

        $controllers->get('/', Controller::class . "::indexAction")
            ->bind('admin');

        $controllers->get('/incomplete', Controller::class . "::incompleteAction")
            ->bind('admin_incomplete');

        $controllers->get('/login_check', Controller::class . "::loginCheckAction");
        $controllers->get('/logout', Controller::class . "::logoutAction");

        $controllers->match('/edit', Controller::class . "::editAction")
            ->convert('photos', 'photos:getPhotosByIds')
            ->bind('edit-photos');

        $controllers->match('/delete/{photo}', Controller::class . "::deleteAction")
            ->convert('photo', 'photos:getPhoto')
            ->bind('delete-photo');

        $controllers->post('/file-upload', Controller::class . "::fileUploadAction")
            ->bind('file-upload');

        return $controllers;
    }
}
