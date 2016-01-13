<?php
namespace Pamplemousse\Photos;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PHPImageWorkshop\ImageWorkshop;

use Pamplemousse\Photos\Entity\Photo;

class Controller
{

    protected $webDir = __DIR__.'/../../../web';

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

        $fileName = $photo->filename;

        $thumbnailDir = $this->webDir . $app['config']['thumbnail_dir'] . $width . 'x' . $height . DIRECTORY_SEPARATOR;
        $thumbnailPath = $thumbnailDir . $fileName;

        if (file_exists($thumbnailPath)) {
            return $this->imageStream($app, $thumbnailPath, $fileName);
        }

        $thumbnail = $this->generateThumbnail($app, $photo, $width, $height, $thumbnailDir);
        return $this->imageStream($app, $thumbnail, $fileName);
    }

    private function imageStream($app, $image, $name)
    {
        $stream = function () use ($image) {
            if (!is_resource($image)) {
                $image = imagecreatefromjpeg($image);
            }
            imagejpeg($image);
        };

        return $app->stream($stream, 200, [
            'Content-type'        => 'image/jpeg',
            'Content-Disposition' => sprintf('filename="%s"', $name),
        ]);
    }

    private function generateThumbnail($app, $photo, $width, $height, $thumbnailDir)
    {
        $fileName = $photo->filename;

        $layer = ImageWorkshop::initFromPath($this->webDir . $app['config']['upload_dir'] . $fileName);
        if ($width == $height) {
            // Square crop
            $layer->cropMaximumInPixel(0, 0, "MM");
        }
        $layer->resizeInPixel($width, $height, true);
        $thumbnail = $layer->getResult();

        $createFolders = true;
        $backgroundColor = null;
        $imageQuality = 95;

        $layer->save($thumbnailDir, $fileName, $createFolders, $backgroundColor, $imageQuality);
        $app['monolog']->addDebug(sprintf("Thumbnail generated: %s/%s", $thumbnailDir, $fileName));

        return $thumbnail;
    }

}
