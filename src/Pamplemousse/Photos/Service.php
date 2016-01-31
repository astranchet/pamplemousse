<?php

namespace Pamplemousse\Photos;

use Symfony\Component\HttpFoundation\Request;

use PHPImageWorkshop\ImageWorkshop;

class Service
{
    const TABLE_NAME = 'pamplemousse__item';

    protected $app;
    protected $config;
    protected $conn;

    public function __construct($app)
    {
        $this->app = $app;
        $this->config = $app['config'];
        $this->conn = $app['db'];
    }

    public function add($filename)
    {
        $this->conn->insert(self::TABLE_NAME, $this->getDataFromFile($filename));
        return $this->getPhoto($this->conn->lastInsertId());
    }

    protected function getDataFromFile($filename)
    {
        $filepath = $this->getUploadDir() . $filename;
        $image = $this->app['imagine']->open($filepath);
        list($width, $height) = getimagesize($filepath);
        $metadata = $image->metadata();

        return [
            'path' => $this->app['config']['upload_dir'] . $filename,
            'date_taken' => $metadata["exif.DateTimeOriginal"],
            'width' => $width,
            'height' => $height,
        ];
    }

    public function getAll()
    {
        $items = $this->conn->fetchAll(sprintf('SELECT * FROM %s WHERE type = ? ORDER BY date_taken DESC', self::TABLE_NAME), array('picture'));

        foreach ($items as $id => $item) {
            yield new Entity\Photo($item);
        }
    }

    public function getPhotosByIds($ids, Request $request = null)
    {
        if (is_null($ids)) {
            $ids = $request->get('ids');
        }

        $statement = $this->conn->executeQuery(sprintf('SELECT * FROM %s WHERE type = "picture" AND id IN (?) ORDER BY date_taken DESC', self::TABLE_NAME),
            [$ids],
            [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY]);
        $items = $statement->fetchAll();

        $photos = [];
        foreach ($items as $id => $item) {
            $photos[$item['id']] = new Entity\Photo($item);
        }

        return $photos;
    }

    public function getPhoto($id)
    {
        $item = $this->conn->fetchAssoc(sprintf('SELECT * FROM %s WHERE id = ?', self::TABLE_NAME), array($id));
        if ($item) {
            return new Entity\Photo($item);
        }
        return false;
    }

    public function findFromFilename($filename)
    {
        $item = $this->conn->fetchAssoc(sprintf('SELECT * FROM %s WHERE path LIKE ?', self::TABLE_NAME), array("%/".$filename));
        if ($item) {
            return new Entity\Photo($item);
        }
        return false;
    }

    public function update($photo)
    {
        $data = [
            'description' => $photo->description,
            'is_favorite' => $photo->is_favorite,
        ];
        return $this->conn->update(self::TABLE_NAME, $data, array('id' => $photo->id));
    }

    public function delete($photo)
    {
        if ($photo->exists()) {
            unlink($photo->getImagePath());
        }
        $thumbnails = $photo->getThumbnails();
        foreach ($thumbnails as $thumbnail) {
            unlink($thumbnail);
        }
        return $this->conn->delete(self::TABLE_NAME, array('id' => $photo->id));
    }

    /**
     * Get thumbnail (or generate it) for given photo and size
     * 
     * @param  Photo $photo
     * @param  int   $width
     * @param  int   $height
     * @return resource
     */
    public function getThumbnail($photo, $width, $height)
    {
        $thumbnailDir = $this->getThumbnailDir($width, $height);
        $thumbnailPath = $thumbnailDir . $photo->filename;

        if (file_exists($thumbnailPath)) {
            return imagecreatefromjpeg($thumbnailPath);
        }

        return $this->generateThumbnail($photo, $width, $height);
    }

    protected function generateThumbnail($photo, $width, $height)
    {
        $layer = ImageWorkshop::initFromPath($photo->getImagePath());
        if ($width == $height) {
            $layer->cropMaximumInPixel(0, 0, "MM"); // Square crop
        }
        $layer->resizeInPixel($width, $height, true);
        $thumbnail = $layer->getResult();

        $thumbnailDir = $this->getThumbnailDir($width, $height);
        $createFolders = true;
        $backgroundColor = null;
        $imageQuality = 95;
        $layer->save($thumbnailDir, $photo->filename, $createFolders, $backgroundColor, $imageQuality);

        $this->app['monolog']->addDebug(sprintf("Thumbnail generated: %s/%s", $thumbnailDir, $photo->filename));

        return $thumbnail;
    }

    public function generateThumbnails($photo)
    {
        foreach ($this->app['config']['thumbnails']['size'] as $size) {
            list($width, $height) = split('x', $size);
            $this->generateThumbnail($photo, $width, $height);
        }
    }

    public function getUploadDir()
    {
        return __DIR__.'/../../../web' . $this->config['upload_dir'] . DIRECTORY_SEPARATOR;
    }

    protected function getThumbnailDir($width, $height)
    {
        return __DIR__.'/../../../web' . $this->config['thumbnails']['dir'] . $width . 'x' . $height . DIRECTORY_SEPARATOR;
    }

}
