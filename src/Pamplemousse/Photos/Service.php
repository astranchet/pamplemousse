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
        $image = $this->app['imagine']->open(__DIR__. '/../../../web/'. $filepath);
        $metadata = $image->metadata();
        $this->conn->insert('pamplemousse__item', [
            'path' => $filepath,
            'date_taken' => $metadata["exif.DateTimeOriginal"],
        ]);
    }

    public function getPhotos()
    {
        $pictures = $this->conn->fetchAll('SELECT * FROM pamplemousse__item WHERE type = ? ORDER BY date_taken DESC', array('picture'));
        $photos = [];
        foreach ($pictures as $id => $picture) {
            $photos[] = [
                'url' => $picture['path'],
                'filename' => basename($picture['path'])
            ];
        }

        return $photos;
    }
}
