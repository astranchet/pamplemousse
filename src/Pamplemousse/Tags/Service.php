<?php

namespace Pamplemousse\Tags;

class Service
{
    const TABLE_NAME = 'pamplemousse__tag';

    protected $app;
    protected $config;
    protected $conn;

    public function __construct($app)
    {
        $this->app = $app;
        $this->config = $app['config'];
        $this->conn = $app['db'];
    }

    public function add($photo)
    {
        foreach ($photo->tags as $tag) {
            $data = [
                'item_id' => $photo->id,
                'tag'   => $tag,
            ];
            $this->conn->insert(self::TABLE_NAME, $data);
        }
    }

    public function delete($photo)
    {
        return $this->conn->delete(self::TABLE_NAME, array('item_id' => $photo->id));
    }

    public function getTags($photoId)
    {
        $items = $this->conn->fetchAll(sprintf('SELECT * FROM %s WHERE item_id = ?', self::TABLE_NAME), array($photoId));

        foreach ($items as $id => $item) {
            yield new Entity\Tag($this->app, $item);
        }
    }

}
