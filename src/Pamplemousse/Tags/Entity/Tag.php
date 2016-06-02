<?php

namespace Pamplemousse\Tags\Entity;

class Tag
{
    public 
        $app,
        $item,
        $tag
    ;

    public function __construct($app, $data)
    {
        $this->app = $app;

        $this->itemId = $data['item_id'];
        $this->tag = $data['tag'];
    }

    public function __toString()
    {
        return $this->tag;
    }
}
