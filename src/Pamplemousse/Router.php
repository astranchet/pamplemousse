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

        $checkFilters = function (Request $request, Application $app) {
            if ($request->get('kids')) {
                foreach ($request->get('kids') as $kid) {
                    if (is_null($app['kids']->getKid($kid))) {
                        return new Response('Bad filter', 404);
                    } 
                }
                return null;
            }
            if ($request->get('tag')) {
                if (!isset($app['config']["tags"][$request->get('tag')])) {
                    return new Response('Bad filter', 404);
                }
                return null;
            }
        };
        $controllers->get('/', Controller::class . "::indexAction")
            ->bind('index')
            ->before($checkFilters)
            ;

        $checkDate = function (Request $request, Application $app) {
            $dates = $app['photos']->getAggregatedDates(\Pamplemousse\Photos\Service::BY_YEAR);
            if (isset($dates[$request->get('year')])) {
                if (array_search($request->get('month'), $dates[$request->get('year')]) !== null) {
                    return null;
                }
            }
            return new Response('Bad date', 404);
        };
        $controllers->get('/date/{year}-{month}', Controller::class . "::byMonthAction")
            ->bind('date')
            ->before($checkDate)
            ;
        $controllers->get('/date/{year}-{month}/next', Controller::class . "::nextMonthAction")
            ->bind('nextDate')
            ->before($checkDate)
            ;
        $controllers->get('/date/{year}-{month}/previous', Controller::class . "::previousMonthAction")
            ->bind('previousDate')
            ->before($checkDate)
            ;

        $controllers->get('/from/{date}', Controller::class . "::fromAction")
            ->bind('from')
            ->before($checkFilters)
            ;

        $controllers->get('/login', Controller::class . "::loginAction");

        return $controllers;
    }
}
