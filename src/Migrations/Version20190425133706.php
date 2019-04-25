<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190425133706 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add Device entity';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EBF396750 FOREIGN KEY (id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE device');
    }
}
