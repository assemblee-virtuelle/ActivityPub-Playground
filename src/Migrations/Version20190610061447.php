<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190610061447 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE object ADD attachment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE object ADD CONSTRAINT FK_A8ADABEC464E68B FOREIGN KEY (attachment_id) REFERENCES object (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8ADABEC464E68B ON object (attachment_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE object DROP FOREIGN KEY FK_A8ADABEC464E68B');
        $this->addSql('DROP INDEX UNIQ_A8ADABEC464E68B ON object');
        $this->addSql('ALTER TABLE object DROP attachment_id');
    }
}
