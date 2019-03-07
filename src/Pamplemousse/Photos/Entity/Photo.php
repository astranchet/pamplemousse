<?php

namespace Pamplemousse\Photos\Entity;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use RecursiveRegexIterator;

class Photo
{
    public 
        $app,
        $id,
        $url,
        $filename,
        $description,
        $date_taken,
        $like,
        $is_favorite,
        $tags,
        $kids,
        $width,
        $height,
        $crop_algorithm,
        $comments
    ;

    public function __construct($app, $data)
    {
        $this->app = $app;

        $this->id = $data['id'];

        $this->url = $data['path'];
        $this->filename = basename($data['path']);

        $this->description = $data['description'];
        $this->date_taken = $data['date_taken'];
        $this->like = $data['like'];
        $this->is_favorite = (boolean) $data['is_favorite'];
        $this->width = $data['width'];
        $this->height = $data['height'];
        $this->crop_algorithm = $data['crop_algorithm'];

        $this->tags = $this->app['tags']->getTags($this->id);
        $this->kids = $this->app['kids']->getKids($this->id);
        $this->comments = $this->app['comments']->getComments($this->id);

        $count = 0;
        foreach ($this->comments as $comment) {
            $count++;
        }
        $this->comments_count = $count;
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

    public function getPrevious()
    {
        return $this->app['photos']->getPreviousPhoto($this);
    }

    public function getNext()
    {
        return $this->app['photos']->getNextPhoto($this);
    }

    public function exists()
    {
        return file_exists($this->getImagePath());
    }

}
