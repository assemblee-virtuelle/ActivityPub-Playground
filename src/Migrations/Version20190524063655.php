<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190524063655 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_92FB68EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE object (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, summary LONGTEXT NOT NULL, content LONGTEXT NOT NULL, class_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actor (id INT NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_447556F9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE following (follower INT NOT NULL, following INT NOT NULL, INDEX IDX_71BF8DE3B9D60946 (follower), INDEX IDX_71BF8DE371BF8DE3 (following), PRIMARY KEY(follower, following)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE authorization (controlled_actor_id INT NOT NULL, controlling_actor_id INT NOT NULL, INDEX IDX_7A6D8BEFD6EEC6FE (controlled_actor_id), INDEX IDX_7A6D8BEF6E512342 (controlling_actor_id), PRIMARY KEY(controlled_actor_id, controlling_actor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application (id INT NOT NULL, api_key VARCHAR(16) NOT NULL, UNIQUE INDEX UNIQ_A45BDDC1C912ED9D (api_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT NOT NULL, uuid VARCHAR(36) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(64) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649D17F50A6 (uuid), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity (id INT NOT NULL, actor_id INT DEFAULT NULL, object_id INT DEFAULT NULL, is_public TINYINT(1) NOT NULL, INDEX IDX_AC74095A10DAF24A (actor_id), UNIQUE INDEX UNIQ_AC74095A232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_receiving_actor (base_activity_id INT NOT NULL, base_actor_id INT NOT NULL, INDEX IDX_EB60FC2388E5966E (base_activity_id), INDEX IDX_EB60FC235A3151B2 (base_actor_id), PRIMARY KEY(base_activity_id, base_actor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE actor ADD CONSTRAINT FK_447556F9BF396750 FOREIGN KEY (id) REFERENCES object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE3B9D60946 FOREIGN KEY (follower) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE371BF8DE3 FOREIGN KEY (following) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE authorization ADD CONSTRAINT FK_7A6D8BEFD6EEC6FE FOREIGN KEY (controlled_actor_id) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE authorization ADD CONSTRAINT FK_7A6D8BEF6E512342 FOREIGN KEY (controlling_actor_id) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1BF396750 FOREIGN KEY (id) REFERENCES object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BF396750 FOREIGN KEY (id) REFERENCES object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A10DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A232D562B FOREIGN KEY (object_id) REFERENCES object (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095ABF396750 FOREIGN KEY (id) REFERENCES object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_receiving_actor ADD CONSTRAINT FK_EB60FC2388E5966E FOREIGN KEY (base_activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_receiving_actor ADD CONSTRAINT FK_EB60FC235A3151B2 FOREIGN KEY (base_actor_id) REFERENCES actor (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE actor DROP FOREIGN KEY FK_447556F9BF396750');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1BF396750');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BF396750');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A232D562B');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095ABF396750');
        $this->addSql('ALTER TABLE following DROP FOREIGN KEY FK_71BF8DE3B9D60946');
        $this->addSql('ALTER TABLE following DROP FOREIGN KEY FK_71BF8DE371BF8DE3');
        $this->addSql('ALTER TABLE authorization DROP FOREIGN KEY FK_7A6D8BEFD6EEC6FE');
        $this->addSql('ALTER TABLE authorization DROP FOREIGN KEY FK_7A6D8BEF6E512342');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A10DAF24A');
        $this->addSql('ALTER TABLE activity_receiving_actor DROP FOREIGN KEY FK_EB60FC235A3151B2');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68EA76ED395');
        $this->addSql('ALTER TABLE activity_receiving_actor DROP FOREIGN KEY FK_EB60FC2388E5966E');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE object');
        $this->addSql('DROP TABLE actor');
        $this->addSql('DROP TABLE following');
        $this->addSql('DROP TABLE authorization');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_receiving_actor');
    }
}
