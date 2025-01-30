<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250120132723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session ADD COLUMN password VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__session AS SELECT id, session_uuid, name, cnt_pages, cnt_entries_per_page, cnt_entries_total FROM session');
        $this->addSql('DROP TABLE session');
        $this->addSql('CREATE TABLE session (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, session_uuid VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, cnt_pages INTEGER NOT NULL, cnt_entries_per_page INTEGER NOT NULL, cnt_entries_total INTEGER NOT NULL)');
        $this->addSql('INSERT INTO session (id, session_uuid, name, cnt_pages, cnt_entries_per_page, cnt_entries_total) SELECT id, session_uuid, name, cnt_pages, cnt_entries_per_page, cnt_entries_total FROM __temp__session');
        $this->addSql('DROP TABLE __temp__session');
    }
}
