<?php

namespace Pamplemousse\Comments;

class Service
{
    const TABLE_NAME = 'pamplemousse__comment';

    protected $app;
    protected $config;
    protected $conn;

    public function __construct($app)
    {
        $this->app = $app;
        $this->config = $app['config'];
        $this->conn = $app['db'];
    }

    public function add($photo, $data)
    {
        $data['date'] = (new \DateTime())->format('Y-m-d H:i');;
        $this->conn->insert(self::TABLE_NAME, $data);
    }

}
