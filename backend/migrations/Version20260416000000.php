<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260416000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create post_vote table with unique (user_id, post_id) to prevent vote stuffing';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE post_vote (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            post_id INT NOT NULL,
            value SMALLINT NOT NULL,
            UNIQUE INDEX uq_post_vote_user_post (user_id, post_id),
            INDEX idx_post_vote_user (user_id),
            INDEX idx_post_vote_post (post_id),
            CONSTRAINT fk_post_vote_user FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE,
            CONSTRAINT fk_post_vote_post FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE post_vote');
    }
}
