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
        $iterator = new \FilesystemIterator(__DIR__.'/../../web/upload/', \FilesystemIterator::SKIP_DOTS);
        $photos = [];
        foreach ($iterator as $file) {
            $photos[$file->getFileName()] = [
                'url' => 'upload/' . $file->getBasename(),
                'title' => $file->getFileName()
            ];
        }
        ksort($photos);

        return $app['twig']->render('index.twig', [
            'photos' => $photos
        ]);
    }

}
