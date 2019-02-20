<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190220155942 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE `pamplemousse__kid` (
          `item_id` int(9) NOT NULL,
          `kid` varchar(80) COLLATE utf8_bin NOT NULL,
          PRIMARY KEY (`item_id`, `kid`)
        )');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE `pamplemousse__kid`;');
    }
}
