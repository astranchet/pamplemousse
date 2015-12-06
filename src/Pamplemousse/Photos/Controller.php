<?php
namespace Pamplemousse\Photos;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PHPImageWorkshop\ImageWorkshop;

class Controller
{

    /**
     * @param  Application $app
     * @param  Request     $request
     * @param  string      $filename
     * @param  int         $width
     * @param  int         $height
     * @return Response
     */
    public function thumbnailAction(Application $app, Request $request, $filename, $width, $height)
    {
        $webDirectory = __DIR__.'/../../../web/';

        $layer = ImageWorkshop::initFromPath($webDirectory . $app['config']['upload_dir'] . $filename);
        $layer->cropMaximumInPixel(0, 0, "MM");
        $layer->resizeInPixel($width, $height);
        $thumbnail = $layer->getResult();

        $destDirectory = $webDirectory . $app['config']['thumbnail_dir'] . DIRECTORY_SEPARATOR . $width . 'x' . $height;
        $createFolders = true;
        $backgroundColor = null;
        $imageQuality = 95;

        $layer->save($destDirectory, $filename, $createFolders, $backgroundColor, $imageQuality);

        ob_start();
        imagejpeg($thumbnail);
        $content = ob_get_clean();

        return new Response($content, 200, [
            'Content-type'        => 'image/jpeg',
            'Content-Disposition' => sprintf('filename="%s"', $filename),
        ]);
    }

}
