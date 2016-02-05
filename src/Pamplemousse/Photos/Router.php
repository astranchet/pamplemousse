<?php
namespace Pamplemousse\Photos;

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

        $controllers->get('/{photo}', Controller::class . "::photoAction")
            ->bind('photo')
            ->convert('photo', 'photos:getPhoto');

        $checkThumbnailsSize = function (Request $request, Application $app) {
            foreach ($app['config']['thumbnails']['size'] as $size) {
                list($width, $height) = split('x', $size);
                if ($width == $request->get('width') && $height == $request->get('height')) {
                    return null;
                }
                if ($width == $request->get('size') && $height == $request->get('size')) {
                    return null;
                }
            }
            return new Response('Bad thumbnail size', 400);
        };

        $checkAlgorithm = function (Request $request, Application $app) {
            if (!in_array($request->get('algorithm'), Service::getCropAlgorithms())) {
                return new Response('Bad algorithm', 400);
            }
            return null;
        };

        $controllers->get('/thumbnail/square/{photo}/{size}/{algorithm}', Controller::class . "::thumbnailSquareAction")
            ->bind('thumbnail-square')
            ->convert('photo', 'photos:getPhoto')
            ->value('algorithm', Service::CROP_CENTER)
            ->before($checkThumbnailsSize)
            ->before($checkAlgorithm)
            ->assert('width', '\d+')
            ;

        $controllers->get('/thumbnail/{photo}/{width}x{height}', Controller::class . "::thumbnailAction")
            ->bind('thumbnail')
            ->convert('photo', 'photos:getPhoto')
            ->value('height', null)
            ->before($checkThumbnailsSize)
            ->assert('width', '\d+')
            ->assert('height', '\d+')
            ;

        return $controllers;
    }
}
