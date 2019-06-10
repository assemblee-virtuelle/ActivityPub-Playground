<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190610084554 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE object DROP FOREIGN KEY FK_A8ADABEC64D218E');
        $this->addSql('ALTER TABLE object ADD CONSTRAINT FK_A8ADABEC64D218E FOREIGN KEY (location_id) REFERENCES place (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE object DROP FOREIGN KEY FK_A8ADABEC64D218E');
        $this->addSql('ALTER TABLE object ADD CONSTRAINT FK_A8ADABEC64D218E FOREIGN KEY (location_id) REFERENCES object (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
