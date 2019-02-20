<?php

namespace Pamplemousse\Kids;

class Service
{
    protected $app;
    protected $config;
    protected $conn;

    public function __construct($app)
    {
        $this->app = $app;
        $this->config = $app['config'];
        $this->conn = $app['db'];
    }

    public function getKids($photoId = null)
    {
        $kids = [];

        foreach ($this->config['kids'] as $kid) {
            $kids[] = $kid['name'];
        }
        
        return $kids;
    }

}
