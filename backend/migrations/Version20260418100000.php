<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260418100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create post_vote table for per-user vote tracking';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE post_vote (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, value SMALLINT NOT NULL, INDEX IDX_54C82AC0A76ED395 (user_id), INDEX IDX_54C82AC04B89032C (post_id), UNIQUE INDEX unique_user_post_vote (user_id, post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post_vote ADD CONSTRAINT FK_54C82AC0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post_vote ADD CONSTRAINT FK_54C82AC04B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post_vote DROP FOREIGN KEY FK_54C82AC0A76ED395');
        $this->addSql('ALTER TABLE post_vote DROP FOREIGN KEY FK_54C82AC04B89032C');
        $this->addSql('DROP TABLE post_vote');
    }
}
