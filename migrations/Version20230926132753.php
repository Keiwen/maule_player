<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230926132753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album ADD import_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE artist ADD import_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE track ADD import_date DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP import_date');
        $this->addSql('ALTER TABLE artist DROP import_date');
        $this->addSql('ALTER TABLE track DROP import_date');
    }
}
