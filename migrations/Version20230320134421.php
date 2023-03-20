<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320134421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock ADD option_name_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B36566069E38BA3 FOREIGN KEY (option_name_id) REFERENCES `option` (id)');
        $this->addSql('CREATE INDEX IDX_4B36566069E38BA3 ON stock (option_name_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B36566069E38BA3');
        $this->addSql('DROP INDEX IDX_4B36566069E38BA3 ON stock');
        $this->addSql('ALTER TABLE stock DROP option_name_id');
    }
}
