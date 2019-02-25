<?php

namespace Pamplemousse\Kids\Entity;

class Kid
{
    public 
        $app,
        $item,
        $kid
    ;

    public function __construct($app, $data)
    {
        $this->app = $app;

        $this->itemId = $data['item_id'];
        $this->kid = $data['kid'];
    }

    public function getConfig()
    {
        foreach ($this->app['config']['kids'] as $kid) {
            if ($kid['name'] == $this->kid) {
                return $kid;
            }
        }
        return null;
    }

    public function __toString()
    {
        return $this->kid;
    }
}
