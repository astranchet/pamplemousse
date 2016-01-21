<?php
namespace Pamplemousse\Photos;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Pamplemousse\Photos\Entity\Photo;

class Controller
{

    /**
     * @param  Application $app
     * @param  Request     $request
     * @param  Photo       $photo
     * @param  int         $width
     * @param  int         $height
     * @return Response
     */
    public function thumbnailAction(Application $app, Request $request, $photo, $width, $height = null)
    {
        if (!$photo) {
            return $app->abort(404);
        }

        $thumbnail = $app['photos']->getThumbnail($photo, $width, $height);
        $stream = function () use ($thumbnail) {
            imagejpeg($thumbnail);
        };

        return $app->stream($stream, 200, [
            'Content-type'        => 'image/jpeg',
            'Content-Disposition' => sprintf('filename="%s"', $photo->filename),
        ]);
    }

}
