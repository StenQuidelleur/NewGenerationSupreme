<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200826082746 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sub_category2 (id INT AUTO_INCREMENT NOT NULL, subcategory_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E4BD468C5DC6FE57 (subcategory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sub_category2 ADD CONSTRAINT FK_E4BD468C5DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES sub_category (id)');
        $this->addSql('ALTER TABLE image CHANGE image image VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE sub_category2');
        $this->addSql('ALTER TABLE image CHANGE image image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
