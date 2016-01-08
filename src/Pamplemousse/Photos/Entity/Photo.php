<?php

namespace Pamplemousse\Photos\Entity;

class Photo
{
    public 
        $id,
        $url,
        $is_favorite,
        $description,
        $filename
    ;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->url = $data['path'];
        $this->is_favorite = (boolean) $data['is_favorite'];
        $this->description = $data['description'];
        $this->filename = basename($data['path']);
    }

}