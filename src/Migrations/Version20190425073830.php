<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190425073830 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'New user entity';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE user 
            (
                id INT AUTO_INCREMENT NOT NULL, 
                email VARCHAR(255) NOT NULL, 
                username VARCHAR(255) NOT NULL, 
                password VARCHAR(64) NOT NULL, 
                roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', 
                first_name VARCHAR(255) NOT NULL, 
                last_name VARCHAR(255) NOT NULL, 
                UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), 
                UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), 
                PRIMARY KEY(id)
            ) 
            DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE user');
    }
}
