<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260412120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add total and items (JSON snapshot) to Order for checkout';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` ADD total NUMERIC(10, 2) DEFAULT NULL, ADD items JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` DROP total, DROP items');
    }
}
