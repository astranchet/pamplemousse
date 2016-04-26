<?php
namespace Pamplemousse;

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
            ->bind('index')
            ;
        $controllers->get('/rss/comment', Controller::class . "::commentRssAction")
            ->bind('rss.comment')
            ;

        $controllers->get('/login', Controller::class . "::loginAction");

        return $controllers;
    }
}
