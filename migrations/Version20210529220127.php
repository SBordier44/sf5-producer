<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210529220127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_item (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', product_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', customer_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', quantity INT NOT NULL, INDEX IDX_F0FE25274584665A (product_id), INDEX IDX_F0FE25279395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE farm (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', producer_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, siret VARCHAR(14) NOT NULL, description LONGTEXT DEFAULT NULL, slug VARCHAR(255) NOT NULL, address_address VARCHAR(255) DEFAULT NULL, address_address_extra VARCHAR(255) DEFAULT NULL, address_zip_code VARCHAR(5) DEFAULT NULL, address_city VARCHAR(255) DEFAULT NULL, address_region VARCHAR(255) DEFAULT NULL, address_country VARCHAR(255) DEFAULT NULL, address_phone VARCHAR(10) DEFAULT NULL, address_position_latitude NUMERIC(16, 13) DEFAULT NULL, address_position_longitude NUMERIC(16, 13) DEFAULT NULL, image_image_path VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_5816D04526E94372 (siret), UNIQUE INDEX UNIQ_5816D045989D9B62 (slug), UNIQUE INDEX UNIQ_5816D04589B658FE (producer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_line (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', order_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', product_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', quantity INT NOT NULL, price_unit_price INT NOT NULL, price_vat NUMERIC(5, 2) NOT NULL, INDEX IDX_9CE58EE18D9F6D38 (order_id), INDEX IDX_9CE58EE14584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', customer_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', farm_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', order_reference BIGINT NOT NULL, state VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', canceled_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', refused_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', settled_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', accepted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', processing_started_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', processing_completed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', issued_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_E52FFDEE122432EB (order_reference), INDEX IDX_E52FFDEE9395C3F3 (customer_id), INDEX IDX_E52FFDEE65FCFA0D (farm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', farm_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name LONGTEXT NOT NULL, description VARCHAR(255) NOT NULL, quantity INT NOT NULL, price_unit_price INT NOT NULL, price_vat NUMERIC(5, 2) NOT NULL, image_image_path VARCHAR(255) DEFAULT NULL, INDEX IDX_D34A04AD65FCFA0D (farm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', farm_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, registered_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', forgotten_password_token CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', forgotten_password_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', discr VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6495E15136F (forgotten_password_token), INDEX IDX_8D93D64965FCFA0D (farm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25274584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25279395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE farm ADD CONSTRAINT FK_5816D04589B658FE FOREIGN KEY (producer_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE14584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE65FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD65FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64965FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE65FCFA0D');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD65FCFA0D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64965FCFA0D');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE18D9F6D38');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25274584665A');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE14584665A');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25279395C3F3');
        $this->addSql('ALTER TABLE farm DROP FOREIGN KEY FK_5816D04589B658FE');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE9395C3F3');
        $this->addSql('DROP TABLE cart_item');
        $this->addSql('DROP TABLE farm');
        $this->addSql('DROP TABLE order_line');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE user');
    }
}
