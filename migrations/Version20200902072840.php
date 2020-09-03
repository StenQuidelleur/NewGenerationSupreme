<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200902072840 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE size (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE size_product (id INT AUTO_INCREMENT NOT NULL, size_id INT DEFAULT NULL, product_id INT DEFAULT NULL, stock_id INT DEFAULT NULL, INDEX IDX_3627D6D5498DA827 (size_id), INDEX IDX_3627D6D54584665A (product_id), UNIQUE INDEX UNIQ_3627D6D5DCD6110 (stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE size_product ADD CONSTRAINT FK_3627D6D5498DA827 FOREIGN KEY (size_id) REFERENCES size (id)');
        $this->addSql('ALTER TABLE size_product ADD CONSTRAINT FK_3627D6D54584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE size_product ADD CONSTRAINT FK_3627D6D5DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE size_product DROP FOREIGN KEY FK_3627D6D5498DA827');
        $this->addSql('DROP TABLE size');
        $this->addSql('DROP TABLE size_product');
    }
}
