<?php
namespace Pamplemousse;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $checkDate = function (Request $request, Application $app) {
            $dates = $app['photos']->getDates();
            if (isset($dates[$request->get('year')])) {
                if (array_search($request->get('month'), $dates[$request->get('year')]) !== null) {
                    return null;
                }
            }
            return new Response('Bad date', 404);
        };
        $controllers->get('/date/{year}/{month}', Controller::class . "::byMonthAction")
            ->bind('date')
            ->before($checkDate)
            ;

        $controllers->get('/from/{date}', Controller::class . "::fromAction")
            ->bind('from')
            ;

        $controllers->get('/rss/comment', Controller::class . "::commentRssAction")
            ->bind('rss.comment')
            ;

        $controllers->get('/login', Controller::class . "::loginAction");

        return $controllers;
    }
}
