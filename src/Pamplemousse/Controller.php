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
        $filter = $request->get('tag');
        $kids = $request->get('kids');

        if (!is_null($filter)) {
            $photos = $app['photos']->getWithTag($filter, self::IMAGE_PER_PAGE);
        } else if (!is_null($kids)) {
            $photos = $app['photos']->getForKids($kids, self::IMAGE_PER_PAGE);
        } else {
            $photos = $app['photos']->getLast(self::IMAGE_PER_PAGE);
        }

        return $app['twig']->render('index.twig', [
            'photos' => $photos,
            'photo_per_page' => self::IMAGE_PER_PAGE
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
     * @param  String      $year
     * @param  String      $month
     * @return Response
     */
    public function nextMonthAction(Application $app, Request $request, $year, $month)
    {
        $year = $request->get('year');
        $month = $request->get('month');

        $nextMonth = $app['photos']->getNextMonth($year, $month);

        if ($nextMonth) {
            return $app->redirect(sprintf('/date/%s', $nextMonth));
        } else {
            return new Response('Bad date', 404);
        }
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @param  String      $year
     * @param  String      $month
     * @return Response
     */
    public function previousMonthAction(Application $app, Request $request, $year, $month)
    {
        $year = $request->get('year');
        $month = $request->get('month');

        $previousMonth = $app['photos']->getPreviousMonth($year, $month);

        if ($previousMonth) {
            return $app->redirect(sprintf('/date/%s', $previousMonth));
        } else {
            return new Response('Bad date', 404);
        }
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @param  string      $date
     * @return Response
     */
    public function fromAction(Application $app, Request $request, $date = null)
    {
        $filter = $request->get('tag');
        $kids = $request->get('kids');

        if (!is_null($filter)) {
            $photos = $app['photos']->getWithTag($filter, self::IMAGE_PER_PAGE, $date);
        } else if (!is_null($kids)) {
            $photos = $app['photos']->getForKids($kids, self::IMAGE_PER_PAGE, $date);
        } else {
            $photos = $app['photos']->getLast(self::IMAGE_PER_PAGE, $date);
        }

        return $app['twig']->render('partials/thumbnails.twig', [
            'photos' => $photos
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
