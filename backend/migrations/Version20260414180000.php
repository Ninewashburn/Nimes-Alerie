<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260414180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add is_verified and email_verify_token to user table, and create reset_password_token table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` ADD is_verified TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE `user` ADD email_verify_token VARCHAR(255) DEFAULT NULL');

        $this->addSql('CREATE TABLE reset_password_token (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            token VARCHAR(255) NOT NULL,
            expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            used TINYINT(1) NOT NULL DEFAULT 0,
            UNIQUE INDEX uq_reset_password_token (token),
            INDEX idx_reset_password_token_user (user_id),
            CONSTRAINT fk_reset_password_token_user FOREIGN KEY (user_id) REFERENCES `user` (id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE reset_password_token');
        $this->addSql('ALTER TABLE `user` DROP COLUMN is_verified');
        $this->addSql('ALTER TABLE `user` DROP COLUMN email_verify_token');
    }
}
