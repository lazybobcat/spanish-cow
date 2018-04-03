<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180403212635 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE asset__translation (id INT AUTO_INCREMENT NOT NULL, asset_id INT DEFAULT NULL, locale_id INT DEFAULT NULL, target LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9383376C5DA1941 (asset_id), INDEX IDX_9383376CE559DFD1 (locale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project__domain (id INT AUTO_INCREMENT NOT NULL, default_locale_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C884F5ED743BF776 (default_locale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE asset__asset (id INT AUTO_INCREMENT NOT NULL, domain_id INT DEFAULT NULL, resname VARCHAR(255) NOT NULL, source VARCHAR(255) NOT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_60BA39DD115F0EE5 (domain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locale__locale (id INT AUTO_INCREMENT NOT NULL, domain_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(2) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E470A0B2115F0EE5 (domain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project__project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE asset__translation ADD CONSTRAINT FK_9383376C5DA1941 FOREIGN KEY (asset_id) REFERENCES asset__asset (id)');
        $this->addSql('ALTER TABLE asset__translation ADD CONSTRAINT FK_9383376CE559DFD1 FOREIGN KEY (locale_id) REFERENCES locale__locale (id)');
        $this->addSql('ALTER TABLE project__domain ADD CONSTRAINT FK_C884F5ED743BF776 FOREIGN KEY (default_locale_id) REFERENCES locale__locale (id)');
        $this->addSql('ALTER TABLE asset__asset ADD CONSTRAINT FK_60BA39DD115F0EE5 FOREIGN KEY (domain_id) REFERENCES project__domain (id)');
        $this->addSql('ALTER TABLE locale__locale ADD CONSTRAINT FK_E470A0B2115F0EE5 FOREIGN KEY (domain_id) REFERENCES project__domain (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE asset__asset DROP FOREIGN KEY FK_60BA39DD115F0EE5');
        $this->addSql('ALTER TABLE locale__locale DROP FOREIGN KEY FK_E470A0B2115F0EE5');
        $this->addSql('ALTER TABLE asset__translation DROP FOREIGN KEY FK_9383376C5DA1941');
        $this->addSql('ALTER TABLE asset__translation DROP FOREIGN KEY FK_9383376CE559DFD1');
        $this->addSql('ALTER TABLE project__domain DROP FOREIGN KEY FK_C884F5ED743BF776');
        $this->addSql('DROP TABLE asset__translation');
        $this->addSql('DROP TABLE project__domain');
        $this->addSql('DROP TABLE asset__asset');
        $this->addSql('DROP TABLE locale__locale');
        $this->addSql('DROP TABLE project__project');
    }
}
