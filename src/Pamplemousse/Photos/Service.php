<?php

namespace Pamplemousse\Photos;

class Service
{
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
        $relativePath = __DIR__. '/../../../web/'. $filepath;
        $image = $this->app['imagine']->open($relativePath);
        list($width, $height) = getimagesize($relativePath);
        $metadata = $image->metadata();
        $this->conn->insert('pamplemousse__item', [
            'path' => $filepath,
            'date_taken' => $metadata["exif.DateTimeOriginal"],
            'width' => $width,
            'height' => $height,
        ]);

        return $this->conn->lastInsertId();
    }

    public function getPhotos()
    {
        $items = $this->conn->fetchAll('SELECT * FROM pamplemousse__item WHERE type = ? ORDER BY date_taken DESC', array('picture'));
        $photos = [];
        foreach ($items as $id => $item) {
            $photos[] = $this->itemToPhoto($item);
        }

        return $photos;
    }

    public function getPhotosByIds($ids)
    {
        $statement = $this->conn->executeQuery(
            'SELECT * FROM pamplemousse__item WHERE type = "picture" AND id IN (?) ORDER BY date_taken DESC',
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
        // TODO : check id exists
        $item = $this->conn->fetchAssoc('SELECT * FROM pamplemousse__item WHERE id = ?', array($id));
        return new Entity\Photo($item);
    }

    public function updatePhoto($id, $photo)
    {
        $data = [
            'description' => $photo->description,
            'is_favorite' => $photo->is_favorite,
        ];
        return $this->conn->update('pamplemousse__item', $data, array('id' => $id));
    }

    // TODO : use entity everywhere
    protected function itemToPhoto($item)
    {
        return [
            'id' => $item['id'],
            'url' => $item['path'],
            'width' => $item['width'],
            'height' => $item['height'],
            'is_favorite' => $item['is_favorite'],
            'description' => $item['description'],
            'filename' => basename($item['path'])
        ];
    }
}
