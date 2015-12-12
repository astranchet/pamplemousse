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
        $iterator = new \FilesystemIterator(__DIR__.'/../../../web/'. $this->config['upload_dir'], \FilesystemIterator::SKIP_DOTS);
        $photos = [];
        foreach ($iterator as $file) {
            $photos[$file->getFileName()] = [
                'url' => $this->config['upload_dir'] . $file->getBasename(),
                'filename' => $file->getFileName()
            ];
        }
        ksort($photos);

        return $photos;
    }
}
