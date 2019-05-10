<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190510102706 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE activity_receiving_actor (activity_id INT NOT NULL, actor_id INT NOT NULL, INDEX IDX_EB60FC2381C06096 (activity_id), INDEX IDX_EB60FC2310DAF24A (actor_id), PRIMARY KEY(activity_id, actor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity_receiving_actor ADD CONSTRAINT FK_EB60FC2381C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_receiving_actor ADD CONSTRAINT FK_EB60FC2310DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE activity_receiving_actor');
    }
}
