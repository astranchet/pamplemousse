<?php
namespace Pamplemousse;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @return Response
     */
    public function photoAction(Application $app, Request $request, $id)
    {
        return $app['twig']->render('photo.twig', [
            'photo' => $app['photos']->getPhoto($id)
        ]);
    }

}
