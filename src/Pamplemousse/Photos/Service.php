<?php

namespace Pamplemousse\Photos;

class Service
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
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
