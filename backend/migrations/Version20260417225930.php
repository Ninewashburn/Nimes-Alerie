<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260417225930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_vote DROP FOREIGN KEY `fk_post_vote_post`');
        $this->addSql('ALTER TABLE post_vote DROP FOREIGN KEY `fk_post_vote_user`');
        $this->addSql('DROP TABLE post_vote');
        $this->addSql('ALTER TABLE `order` RENAME INDEX fk_f5299398a76ed395 TO IDX_F5299398A76ED395');
        $this->addSql('ALTER TABLE product CHANGE is_active is_active TINYINT NOT NULL');
        $this->addSql('DROP INDEX uq_rate_user_product ON rate');
        $this->addSql('ALTER TABLE rate CHANGE rate rate INT DEFAULT NULL, CHANGE testimonial testimonial LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE reset_password_token CHANGE expires_at expires_at DATETIME NOT NULL, CHANGE used used TINYINT NOT NULL');
        $this->addSql('ALTER TABLE reset_password_token RENAME INDEX uq_reset_password_token TO UNIQ_452C9EC55F37A13B');
        $this->addSql('ALTER TABLE reset_password_token RENAME INDEX idx_reset_password_token_user TO IDX_452C9EC5A76ED395');
        $this->addSql('ALTER TABLE user CHANGE is_verified is_verified TINYINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post_vote (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, value SMALLINT NOT NULL, UNIQUE INDEX uq_post_vote_user_post (user_id, post_id), INDEX idx_post_vote_post (post_id), INDEX idx_post_vote_user (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE post_vote ADD CONSTRAINT `fk_post_vote_post` FOREIGN KEY (post_id) REFERENCES post (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_vote ADD CONSTRAINT `fk_post_vote_user` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `order` RENAME INDEX idx_f5299398a76ed395 TO FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE product CHANGE is_active is_active TINYINT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE rate CHANGE rate rate INT NOT NULL, CHANGE testimonial testimonial VARCHAR(2000) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX uq_rate_user_product ON rate (user_id, product_id)');
        $this->addSql('ALTER TABLE reset_password_token CHANGE expires_at expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE used used TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE reset_password_token RENAME INDEX idx_452c9ec5a76ed395 TO idx_reset_password_token_user');
        $this->addSql('ALTER TABLE reset_password_token RENAME INDEX uniq_452c9ec55f37a13b TO uq_reset_password_token');
        $this->addSql('ALTER TABLE user CHANGE is_verified is_verified TINYINT DEFAULT 0 NOT NULL');
    }
}
