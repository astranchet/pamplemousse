<?php

namespace Pamplemousse\Photos;

class Service
{

    public function getPhotos()
    {
        $iterator = new \FilesystemIterator(__DIR__.'/../../../web/upload/', \FilesystemIterator::SKIP_DOTS);
        $photos = [];
        foreach ($iterator as $file) {
            $photos[$file->getFileName()] = [
                'url' => 'upload/' . $file->getBasename(),
                'title' => $file->getFileName()
            ];
        }
        ksort($photos);

        return $photos;
    }
}
