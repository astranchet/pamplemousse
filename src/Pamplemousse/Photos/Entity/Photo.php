<?php

namespace Pamplemousse\Photos\Entity;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use RecursiveRegexIterator;

class Photo
{
    public 
        $id,
        $url,
        $filename,
        $description,
        $date_taken,
        $like,
        $is_favorite,
        $width,
        $height
    ;

    public function __construct($data)
    {
        $this->id = $data['id'];

        $this->url = $data['path'];
        $this->filename = basename($data['path']);

        $this->description = $data['description'];
        $this->date_taken = $data['date_taken'];
        $this->like = $data['like'];
        $this->is_favorite = (boolean) $data['is_favorite'];
        $this->width = $data['width'];
        $this->height = $data['height'];
    }

    public function getImagePath()
    {
        return __DIR__.'/../../../../web' . $this->url;
    }

    public function getThumbnails()
    {
        $directory = new RecursiveDirectoryIterator(__DIR__.'/../../../../web/thumbnail');
        $regex = sprintf('/%s$/i', addcslashes($this->filename, '\.'));
        $iterator = new RegexIterator(new RecursiveIteratorIterator($directory), $regex, RecursiveRegexIterator::GET_MATCH);

        foreach ($iterator as $thumbnailPath => $thumbnailName) {
            yield $thumbnailPath;
        }
    }

    public function exists()
    {
        return file_exists($this->getImagePath());
    }

}
