<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320152327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660C964ABE2');
        $this->addSql('DROP INDEX UNIQ_4B365660C964ABE2 ON stock');
        $this->addSql('ALTER TABLE stock DROP product_option_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock ADD product_option_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660C964ABE2 FOREIGN KEY (product_option_id) REFERENCES `option` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4B365660C964ABE2 ON stock (product_option_id)');
    }
}
