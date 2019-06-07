<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190607093530 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tag (tag_id INT NOT NULL, tagged_object_id INT NOT NULL, INDEX IDX_389B783BAD26311 (tag_id), INDEX IDX_389B7835B7EB2CD (tagged_object_id), PRIMARY KEY(tag_id, tagged_object_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783BAD26311 FOREIGN KEY (tag_id) REFERENCES object (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B7835B7EB2CD FOREIGN KEY (tagged_object_id) REFERENCES object (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tag');
    }
}
