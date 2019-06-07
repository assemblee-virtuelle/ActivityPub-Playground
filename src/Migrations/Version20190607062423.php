<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190607062423 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE object ADD location_id INT DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD url VARCHAR(255) DEFAULT NULL, ADD published DATETIME DEFAULT NULL, ADD updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE object ADD CONSTRAINT FK_A8ADABEC64D218E FOREIGN KEY (location_id) REFERENCES object (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8ADABEC64D218E ON object (location_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE object DROP FOREIGN KEY FK_A8ADABEC64D218E');
        $this->addSql('DROP INDEX UNIQ_A8ADABEC64D218E ON object');
        $this->addSql('ALTER TABLE object DROP location_id, DROP image, DROP url, DROP published, DROP updated');
    }
}
