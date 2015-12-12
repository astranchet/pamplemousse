<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151212161036 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE `pamplemousse__item` (
          `id` int(9) NOT NULL AUTO_INCREMENT,
          `file` varchar(255) COLLATE utf8_bin NOT NULL,
          `description` varchar(80) COLLATE utf8_bin DEFAULT NULL,
          `date` timestamp NULL DEFAULT NULL,
          `like` int(6) NOT NULL DEFAULT \'0\',
          `type` varchar(7) COLLATE utf8_bin NOT NULL DEFAULT \'picture\',
          PRIMARY KEY (`id`)
        )');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE `pamplemousse__item`;');
    }
}
