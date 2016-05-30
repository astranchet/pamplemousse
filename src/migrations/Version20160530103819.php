<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160530103819 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE `pamplemousse__tag` (
          `item_id` int(9) NOT NULL,
          `tag` varchar(80) COLLATE utf8_bin NOT NULL,
          PRIMARY KEY (`item_id`, `tag`)
        )');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE `pamplemousse__tag`;');
    }
}
