<?php
namespace Pamplemousse;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Pamplemousse\Photos\Entity\Photo;

class Controller
{
    const IMAGE_PER_PAGE = 50;

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return Response
     */
    public function indexAction(Application $app, Request $request)
    {
        $filter = $request->get('filter');

        if (is_null($filter)) {
            $photos = $app['photos']->getLast(self::IMAGE_PER_PAGE);
        } else {
            $photos = $app['photos']->getWithTag($filter);
        }

        return $app['twig']->render('index.twig', [
            'photos' => $photos,
        ]);
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @param  String      $year
     * @param  String      $month
     * @return Response
     */
    public function byMonthAction(Application $app, Request $request, $year, $month)
    {
        $year = $request->get('year');
        $month = $request->get('month');
        $photos = $app['photos']->getForDate($month, $year);

        return $app['twig']->render('byMonth.twig', [
            'photos' => $photos,
            'current_date' => ['year' => $year, 'month' => $month]
        ]);
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @param  string      $date
     * @return Response
     */
    public function fromAction(Application $app, Request $request, $date = null)
    {
        return $app['twig']->render('partials/thumbnails.twig', [
            'photos' => $app['photos']->getLast(self::IMAGE_PER_PAGE, $date)
        ]);
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return Response
     */
    public function commentRssAction(Application $app, Request $request)
    {
        return $app['twig']->render('rss.twig', [
            'comments' => $app['comments']->getLast()
        ]);
    }
    

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return Response
     */
    public function loginAction(Application $app, Request $request)
    {
        return $app['twig']->render('login.twig', [
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ]);
    }

}
