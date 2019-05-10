<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190510095233 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE actor CHANGE type type ENUM(\'Application\', \'Group\', \'Organization\', \'Person\', \'Service\') NOT NULL COMMENT \'(DC2Type:actor_type)\'');
        $this->addSql('ALTER TABLE activity ADD object_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A232D562B FOREIGN KEY (object_id) REFERENCES activity_object (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC74095A232D562B ON activity (object_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A232D562B');
        $this->addSql('DROP INDEX UNIQ_AC74095A232D562B ON activity');
        $this->addSql('ALTER TABLE activity DROP object_id');
        $this->addSql('ALTER TABLE actor CHANGE type type VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
