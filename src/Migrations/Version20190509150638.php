<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190509150638 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'New tables';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE actor (
            id INT AUTO_INCREMENT NOT NULL,
            username VARCHAR(255) NOT NULL,
            summary LONGTEXT NOT NULL,
            type VARCHAR(255) NOT NULL,
            UNIQUE INDEX UNIQ_447556F9F85E0677 (username),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE following (
            follower INT NOT NULL,
            following INT NOT NULL,
            INDEX IDX_71BF8DE3B9D60946 (follower),
            INDEX IDX_71BF8DE371BF8DE3 (following),
            PRIMARY KEY(follower, following)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE application (
            id INT NOT NULL,
            api_key VARCHAR(16) NOT NULL,
            UNIQUE INDEX UNIQ_A45BDDC1C912ED9D (api_key),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE activity (
            id INT AUTO_INCREMENT NOT NULL,
            type ENUM(\'Accept\', \'Create\', \'Follow\', \'Undo\') NOT NULL COMMENT \'(DC2Type:activity_type)\',
            is_public TINYINT(1) NOT NULL, PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE activity_object (
            id INT AUTO_INCREMENT NOT NULL,
            type ENUM(\'Note\') NOT NULL COMMENT \'(DC2Type:activity_object_type)\',
            content LONGTEXT NOT NULL, PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE3B9D60946 FOREIGN KEY (follower) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE371BF8DE3 FOREIGN KEY (following) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1BF396750 FOREIGN KEY (id) REFERENCES actor (id) ON DELETE CASCADE');

        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user DROP username');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BF396750 FOREIGN KEY (id) REFERENCES actor (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {

    }
}
