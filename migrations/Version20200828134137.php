<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200828134137 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sub_category2 DROP FOREIGN KEY FK_E4BD468C5DC6FE57');
        $this->addSql('DROP INDEX IDX_E4BD468C5DC6FE57 ON sub_category2');
        $this->addSql('ALTER TABLE sub_category2 CHANGE subcategory_id sub_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_category2 ADD CONSTRAINT FK_E4BD468CF7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_category (id)');
        $this->addSql('CREATE INDEX IDX_E4BD468CF7BFE87C ON sub_category2 (sub_category_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sub_category2 DROP FOREIGN KEY FK_E4BD468CF7BFE87C');
        $this->addSql('DROP INDEX IDX_E4BD468CF7BFE87C ON sub_category2');
        $this->addSql('ALTER TABLE sub_category2 CHANGE sub_category_id subcategory_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_category2 ADD CONSTRAINT FK_E4BD468C5DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES sub_category (id)');
        $this->addSql('CREATE INDEX IDX_E4BD468C5DC6FE57 ON sub_category2 (subcategory_id)');
    }
}
