<?php
namespace Pamplemousse;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Pamplemousse\Photos\Entity\Photo;

class Controller
{

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return Response
     */
    public function indexAction(Application $app, Request $request)
    {
        return $app['twig']->render('index.twig', [
            'photos' => $app['photos']->getPhotos()
        ]);
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @param  Photo       $photo
     * @return Response
     */
    public function photoAction(Application $app, Request $request, Photo $photo)
    {
        return $app['twig']->render('photo.twig', [
            'photo' => $photo
        ]);
    }

}
