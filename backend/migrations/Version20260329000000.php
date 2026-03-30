<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260329000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix delivery_date column type from VARCHAR to DATE';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE delivery CHANGE delivery_date delivery_date DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE delivery CHANGE delivery_date delivery_date VARCHAR(255) NOT NULL');
    }
}
