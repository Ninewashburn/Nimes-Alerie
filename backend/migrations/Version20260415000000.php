<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260415000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rate: unique constraint user+product, NOT NULL rate, column type change text→varchar(2000)';
    }

    public function up(Schema $schema): void
    {
        // Make rate NOT NULL (was nullable: true)
        $this->addSql('ALTER TABLE rate CHANGE rate rate INT NOT NULL');

        // Limit testimonial to 2000 chars (was TEXT/unlimited)
        $this->addSql('ALTER TABLE rate CHANGE testimonial testimonial VARCHAR(2000) DEFAULT NULL');

        // Unique constraint: one review per user per product
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT uq_rate_user_product UNIQUE (user_id, product_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rate DROP INDEX uq_rate_user_product');
        $this->addSql('ALTER TABLE rate CHANGE rate rate INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rate CHANGE testimonial testimonial LONGTEXT DEFAULT NULL');
    }
}
