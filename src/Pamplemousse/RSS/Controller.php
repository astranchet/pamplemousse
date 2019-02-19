<?php
namespace Pamplemousse\RSS;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Pamplemousse\Photos\Entity\Photo;

class Controller
{

    const IMAGE_PER_FEED = 25;

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return Response
     */
    public function commentRssAction(Application $app, Request $request)
    {
        return $app['twig']->render('rss/comments.twig', [
            'comments' => $app['comments']->getLast()
        ]);
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return Response
     */
    public function photosRssAction(Application $app, Request $request)
    {
        return $app['twig']->render('rss/photos.twig', [
            'photos' => $app['photos']->getLast(self::IMAGE_PER_FEED)
        ]);
    }

}
