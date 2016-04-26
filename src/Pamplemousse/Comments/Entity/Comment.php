<?php

namespace Pamplemousse\Comments\Entity;

class Comment
{
    public 
        $app,
        $id,
        $item,
        $name,
        $comment,
        $date,
        $photoId
    ;

    public function __construct($app, $data)
    {
        $this->app = $app;
        $this->id = $data['id'];

        $this->name = $data['name'];
        $this->comment = $data['comment'];
        $this->date = $data['date'];

        $this->photoId = $data['item_id'];
    }

}
