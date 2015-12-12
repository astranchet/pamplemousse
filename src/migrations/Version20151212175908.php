<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151212175908 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE `pamplemousse__item` CHANGE `file` `path` varchar(255)');
        $this->addSql('ALTER TABLE `pamplemousse__item` CHANGE `date` `date_taken` timestamp');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE `pamplemousse__item` CHANGE `date_taken` `date` timestamp');
        $this->addSql('ALTER TABLE `pamplemousse__item` CHANGE `path` `file` varchar(255)');
    }
}
