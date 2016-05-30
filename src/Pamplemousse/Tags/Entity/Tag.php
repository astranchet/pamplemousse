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
        $this->item = $data['item'];
        $this->tag = $data['tag'];
    }

}
