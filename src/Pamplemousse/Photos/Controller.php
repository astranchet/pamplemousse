<?php
namespace Pamplemousse\Photos;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PHPImageWorkshop\ImageWorkshop;

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

        $filename = $photo->filename;

        $webDirectory = __DIR__.'/../../../web';
        $destDirectory = $webDirectory . $app['config']['thumbnail_dir'] . $width . 'x' . $height . DIRECTORY_SEPARATOR;

        $destFile = $destDirectory . $filename;
        if (file_exists($destFile)) {
            $thumbnail = imagecreatefromjpeg($destFile);
            ob_start();
            imagejpeg($thumbnail);
            $content = ob_get_clean();

            return new Response($content, 200, [
                'Content-type'        => 'image/jpeg',
                'Content-Disposition' => sprintf('filename="%s"', $filename),
            ]);
        }

        $layer = ImageWorkshop::initFromPath($webDirectory . $app['config']['upload_dir'] . $filename);
        if ($width == $height) {
            // Square crop
            $layer->cropMaximumInPixel(0, 0, "MM");
        }
        $layer->resizeInPixel($width, $height, true);
        $thumbnail = $layer->getResult();

        $createFolders = true;
        $backgroundColor = null;
        $imageQuality = 95;

        $layer->save($destDirectory, $filename, $createFolders, $backgroundColor, $imageQuality);
        $app['monolog']->addDebug(sprintf("Thumbnail generated: %s/%s", $destDirectory, $filename));

        ob_start();
        imagejpeg($thumbnail);
        $content = ob_get_clean();

        return new Response($content, 200, [
            'Content-type'        => 'image/jpeg',
            'Content-Disposition' => sprintf('filename="%s"', $filename),
        ]);
    }

}
