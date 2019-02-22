<?php

namespace Pamplemousse\Kids;

class Service
{
    const TABLE_NAME = 'pamplemousse__kid';

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
        foreach ($photo->kids as $kid) {
            $data = [
                'item_id' => $photo->id,
                'kid'   => $kid,
            ];
            $this->conn->insert(self::TABLE_NAME, $data);
        }
    }

    public function delete($photo)
    {
        return $this->conn->delete(self::TABLE_NAME, array('item_id' => $photo->id));
    }

    public function getKids($photoId = null)
    {
        $kids = [];
        if (is_null($photoId)) {
            foreach ($this->config['kids'] as $kid) {
                $kids[$kid['name']] = $kid['name'];
            }
            return $kids;
        }

        $items = $this->conn->fetchAll(sprintf('SELECT * FROM %s WHERE item_id = ?', self::TABLE_NAME), array($photoId));

        foreach ($items as $id => $item) {
            $kids[] = new Entity\Kid($this->app, $item);
        }

        return $kids;
    }

    public function getKid($name)
    {
        foreach ($this->config['kids'] as $kid) {
            if ($kid['name'] == $name) {
                return $kid;
            }
        }
        return null;
    }

}
