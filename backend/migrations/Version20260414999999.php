<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Manual migration to fix reserved keyword 'READ' in ContactMessage entity.
 */
final class Version20260414999999 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename contact_message.read to is_read to avoid MySQL reserved keyword conflict.';
    }

    public function up(Schema $schema): void
    {
        // Check if the column exists to avoid error if already renamed
        $table = $schema->getTable('contact_message');
        if ($table->hasColumn('read')) {
            $this->addSql('ALTER TABLE contact_message CHANGE `read` is_read TINYINT(1) DEFAULT 0 NOT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('contact_message');
        if ($table->hasColumn('is_read')) {
            $this->addSql('ALTER TABLE contact_message CHANGE is_read `read` TINYINT(1) DEFAULT 0 NOT NULL');
        }
    }
}
