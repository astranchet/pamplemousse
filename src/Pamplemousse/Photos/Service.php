<?php

namespace Pamplemousse\Photos;

class Service
{
    protected $config;
    protected $conn;

    public function __construct($config, $conn)
    {
        $this->config = $config;
        $this->conn = $conn;
    }

    public function add($filepath)
    {
        $this->conn->insert('pamplemousse__item', [
            'file' => $filepath,
            'date' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getPhotos()
    {
        $pictures = $this->conn->fetchAll('SELECT * FROM pamplemousse__item WHERE type = ?', array('picture'));
        $photos = [];
        foreach ($pictures as $id => $picture) {
            $photos[basename($picture['file'])] = [
                'url' => $picture['file'],
                'filename' => basename($picture['file'])
            ];
        }
        ksort($photos);

        return $photos;
    }
}
