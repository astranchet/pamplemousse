<?php

namespace Pamplemousse\Photos;

use Symfony\Component\HttpFoundation\Request;

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

    public function add($filepath)
    {
        $this->conn->insert(self::TABLE_NAME, $this->getDataFromFile($filepath));
        return $this->conn->lastInsertId();
    }

    protected function getDataFromFile($filepath)
    {
        $relativePath = __DIR__. '/../../../web/'. $filepath;
        $image = $this->app['imagine']->open($relativePath);
        list($width, $height) = getimagesize($relativePath);
        $metadata = $image->metadata();

        return [
            'path' => $filepath,
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
        unlink($photo->getImagePath());
        $thumbnails = $photo->getThumbnails();
        foreach ($thumbnails as $thumbnail) {
            unlink($thumbnail);
        }
        return $this->conn->delete(self::TABLE_NAME, array('id' => $photo->id));
    }

}
