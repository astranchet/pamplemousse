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
        return $app['twig']->render('index.twig', [
            'photos' => $app['photos']->getLast(self::IMAGE_PER_PAGE),
            'tags'   => $app['tags']->getTags()
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
