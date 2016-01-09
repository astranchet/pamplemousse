<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160109181501 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE `pamplemousse__item` ADD COLUMN width INT NOT NULL DEFAULT 0;');
        $this->addSql('ALTER TABLE `pamplemousse__item` ADD COLUMN height INT NOT NULL DEFAULT 0;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE `pamplemousse__item` DROP COLUMN width;');
        $this->addSql('ALTER TABLE `pamplemousse__item` DROP COLUMN height;');
    }
}
