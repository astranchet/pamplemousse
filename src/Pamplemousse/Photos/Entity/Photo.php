<?php

namespace Pamplemousse\Photos\Entity;

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

}
