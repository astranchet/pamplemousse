<?php
namespace Pamplemousse\RSS;

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

        $controllers->get('/comment', Controller::class . "::commentRssAction")
            ->bind('rss.comment')
            ;
        $controllers->get('/photos', Controller::class . "::photosRssAction")
            ->bind('rss.photos')
            ;

        return $controllers;
    }
}
