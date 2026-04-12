<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260412000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix entities: User (gender, postalCode), Product (priceHT, priceTTC, isActive), Category (parent FK), Order (status enum), Bill (payment enum), Delivery (address fields)';
    }

    public function up(Schema $schema): void
    {
        // User: add gender and postal_code
        $this->addSql('ALTER TABLE user ADD gender VARCHAR(10) DEFAULT NULL, ADD postal_code VARCHAR(10) DEFAULT NULL');

        // Product: replace price with price_ht + price_ttc, add is_active
        $this->addSql('ALTER TABLE product ADD price_ht NUMERIC(10, 2) NOT NULL, ADD price_ttc NUMERIC(10, 2) NOT NULL, ADD is_active TINYINT(1) NOT NULL DEFAULT 1');
        $this->addSql('UPDATE product SET price_ht = price, price_ttc = ROUND(price * 1.20, 2)');
        $this->addSql('ALTER TABLE product DROP price');

        // Category: replace parent (string) with parent_id (FK)
        $this->addSql('ALTER TABLE category DROP parent');
        $this->addSql('ALTER TABLE category ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_64C19C1727ACA70 ON category (parent_id)');

        // Order: resize status column (now stores enum string values)
        $this->addSql('ALTER TABLE `order` CHANGE status status VARCHAR(50) NOT NULL DEFAULT \'pending\'');

        // Bill: resize payment column (now stores enum string values)
        $this->addSql('ALTER TABLE bill CHANGE payment payment VARCHAR(20) NOT NULL DEFAULT \'card\'');

        // Delivery: add address fields
        $this->addSql('ALTER TABLE delivery ADD delivery_address VARCHAR(255) NOT NULL DEFAULT \'\', ADD delivery_city VARCHAR(100) NOT NULL DEFAULT \'\', ADD delivery_postal_code VARCHAR(10) NOT NULL DEFAULT \'\', ADD delivery_country VARCHAR(100) NOT NULL DEFAULT \'\'');
    }

    public function down(Schema $schema): void
    {
        // Delivery: remove address fields
        $this->addSql('ALTER TABLE delivery DROP delivery_address, DROP delivery_city, DROP delivery_postal_code, DROP delivery_country');

        // Bill: restore original payment column
        $this->addSql('ALTER TABLE bill CHANGE payment payment VARCHAR(255) NOT NULL');

        // Order: restore original status column
        $this->addSql('ALTER TABLE `order` CHANGE status status VARCHAR(255) NOT NULL');

        // Category: restore parent string column
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('DROP INDEX IDX_64C19C1727ACA70 ON category');
        $this->addSql('ALTER TABLE category DROP parent_id');
        $this->addSql('ALTER TABLE category ADD parent VARCHAR(255) NOT NULL');

        // Product: restore price, remove priceHT/priceTTC/isActive
        $this->addSql('ALTER TABLE product ADD price NUMERIC(10, 2) NOT NULL');
        $this->addSql('UPDATE product SET price = price_ttc');
        $this->addSql('ALTER TABLE product DROP price_ht, DROP price_ttc, DROP is_active');

        // User: remove gender and postal_code
        $this->addSql('ALTER TABLE user DROP gender, DROP postal_code');
    }
}
