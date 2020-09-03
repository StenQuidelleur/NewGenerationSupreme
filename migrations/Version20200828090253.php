<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200828090253 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD318B80D4');
        $this->addSql('DROP INDEX IDX_D34A04AD318B80D4 ON product');
        $this->addSql('ALTER TABLE product CHANGE subcategory2_id sub_category2_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD9D9D0082 FOREIGN KEY (sub_category2_id) REFERENCES sub_category2 (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD9D9D0082 ON product (sub_category2_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD9D9D0082');
        $this->addSql('DROP INDEX IDX_D34A04AD9D9D0082 ON product');
        $this->addSql('ALTER TABLE product CHANGE sub_category2_id subcategory2_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD318B80D4 FOREIGN KEY (subcategory2_id) REFERENCES sub_category2 (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD318B80D4 ON product (subcategory2_id)');
    }
}
