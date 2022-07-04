<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704193535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE stock');
        $this->addSql('ALTER TABLE `option` DROP FOREIGN KEY FK_5A8600B0908E2FFE');
        $this->addSql('DROP INDEX IDX_5A8600B0908E2FFE ON `option`');
        $this->addSql('ALTER TABLE `option` DROP specification_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, product_option_id INT DEFAULT NULL, stock INT NOT NULL, INDEX IDX_4B3656604584665A (product_id), UNIQUE INDEX UNIQ_4B365660C964ABE2 (product_option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656604584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660C964ABE2 FOREIGN KEY (product_option_id) REFERENCES `option` (id)');
        $this->addSql('ALTER TABLE `option` ADD specification_id INT NOT NULL');
        $this->addSql('ALTER TABLE `option` ADD CONSTRAINT FK_5A8600B0908E2FFE FOREIGN KEY (specification_id) REFERENCES specification (id)');
        $this->addSql('CREATE INDEX IDX_5A8600B0908E2FFE ON `option` (specification_id)');
    }
}
