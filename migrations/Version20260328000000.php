<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to fix column types after Phase 1 entity corrections.
 * - Product.price: VARCHAR(10) -> DECIMAL(10,2)
 * - Product.quantity: VARCHAR(10) -> INT
 * - CartLine.quantity: VARCHAR(10) -> INT
 */
final class Version20260328000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix column types: Product price to DECIMAL, Product/CartLine quantity to INT';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE quantity quantity INT NOT NULL');
        $this->addSql('ALTER TABLE cart_line CHANGE quantity quantity INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product CHANGE price price VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE quantity quantity VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE cart_line CHANGE quantity quantity VARCHAR(10) NOT NULL');
    }
}
