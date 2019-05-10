<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190510095832 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE activity ADD actor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A10DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id)');
        $this->addSql('CREATE INDEX IDX_AC74095A10DAF24A ON activity (actor_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A10DAF24A');
        $this->addSql('DROP INDEX IDX_AC74095A10DAF24A ON activity');
        $this->addSql('ALTER TABLE activity DROP actor_id');
    }
}
