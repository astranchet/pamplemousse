<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160304095550 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE `pamplemousse__comment` (
          `id` int(9) NOT NULL AUTO_INCREMENT,
          `item_id` int(9) NOT NULL,
          `name` varchar(80) COLLATE utf8_bin DEFAULT NULL,
          `comment` text COLLATE utf8_bin DEFAULT NULL,
          `date` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
        )');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE `pamplemousse__comment`;');
    }
}
