<?php

namespace Pamplemousse\Comments\Entity;

class Comment
{
    public 
        $id,
        $item,
        $name,
        $comment,
        $date
    ;

    public function __construct($data)
    {
        $this->id = $data['id'];

        $this->name = $data['name'];
        $this->comment = $data['comment'];
        $this->date = $data['date'];
    }

}
