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

    public function getComments($photoId)
    {
        $items = $this->conn->fetchAll(sprintf('SELECT * FROM %s WHERE item_id = ?', self::TABLE_NAME), array($photoId));

        foreach ($items as $id => $item) {
            yield new Entity\Comment($this->app, $item);
        }
    }

    public function getLast($limit = 10)
    {
        $items = $this->conn->fetchAll(sprintf('SELECT * FROM %s ORDER BY ID DESC LIMIT 10', self::TABLE_NAME, $limit));

        foreach ($items as $id => $item) {
            yield new Entity\Comment($this->app, $item);
        }
    }

}
