<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180608072945 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE asset__translation (id INT AUTO_INCREMENT NOT NULL, asset_id INT DEFAULT NULL, locale_id INT DEFAULT NULL, target LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9383376C5DA1941 (asset_id), INDEX IDX_9383376CE559DFD1 (locale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locale__locale (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(2) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project__domain (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C884F5ED166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project__domain_locales (domain_id INT NOT NULL, locale_id INT NOT NULL, INDEX IDX_E30465DC115F0EE5 (domain_id), INDEX IDX_E30465DCE559DFD1 (locale_id), PRIMARY KEY(domain_id, locale_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project__project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project__users (project_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_891FA23F166D1F9C (project_id), INDEX IDX_891FA23FA76ED395 (user_id), PRIMARY KEY(project_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user__user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(190) NOT NULL, password VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_32745D0AE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE asset__asset (id INT AUTO_INCREMENT NOT NULL, domain_id INT DEFAULT NULL, resname VARCHAR(190) NOT NULL, source LONGTEXT NOT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_60BA39DD115F0EE5 (domain_id), INDEX resname_idx (resname), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE asset__translation ADD CONSTRAINT FK_9383376C5DA1941 FOREIGN KEY (asset_id) REFERENCES asset__asset (id)');
        $this->addSql('ALTER TABLE asset__translation ADD CONSTRAINT FK_9383376CE559DFD1 FOREIGN KEY (locale_id) REFERENCES locale__locale (id)');
        $this->addSql('ALTER TABLE project__domain ADD CONSTRAINT FK_C884F5ED166D1F9C FOREIGN KEY (project_id) REFERENCES project__project (id)');
        $this->addSql('ALTER TABLE project__domain_locales ADD CONSTRAINT FK_E30465DC115F0EE5 FOREIGN KEY (domain_id) REFERENCES project__domain (id)');
        $this->addSql('ALTER TABLE project__domain_locales ADD CONSTRAINT FK_E30465DCE559DFD1 FOREIGN KEY (locale_id) REFERENCES locale__locale (id)');
        $this->addSql('ALTER TABLE project__users ADD CONSTRAINT FK_891FA23F166D1F9C FOREIGN KEY (project_id) REFERENCES project__project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project__users ADD CONSTRAINT FK_891FA23FA76ED395 FOREIGN KEY (user_id) REFERENCES user__user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE asset__asset ADD CONSTRAINT FK_60BA39DD115F0EE5 FOREIGN KEY (domain_id) REFERENCES project__domain (id)');

        $this->addSql('INSERT INTO `locale__locale` (`name`, `code`, `created_at`, `updated_at`) VALUES (\'Dansk\', \'da\', NOW(), NOW()), (\'Deutsch\', \'de\', NOW(), NOW()), (\'Ελληνικά\', \'el\', NOW(), NOW()), (\'English\', \'en\', NOW(), NOW()), (\'Español\', \'es\', NOW(), NOW()), (\'Euskara\', \'eu\', NOW(), NOW()), (\'Suomen kieli\', \'fi\', NOW(), NOW()), (\'Français\', \'fr\', NOW(), NOW()), (\'Italiano\', \'it\', NOW(), NOW()), (\'Lëtzebuergesch\', \'lb\', NOW(), NOW()), (\'Nederlands\', \'nl\', NOW(), NOW()), (\'Norsk\', \'no\', NOW(), NOW()), (\'Português\', \'pt\', NOW(), NOW()), (\'русский язык\', \'ru\', NOW(), NOW()), (\'Svenska\', \'sv\', NOW(), NOW())');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE asset__translation DROP FOREIGN KEY FK_9383376CE559DFD1');
        $this->addSql('ALTER TABLE project__domain_locales DROP FOREIGN KEY FK_E30465DCE559DFD1');
        $this->addSql('ALTER TABLE project__domain_locales DROP FOREIGN KEY FK_E30465DC115F0EE5');
        $this->addSql('ALTER TABLE asset__asset DROP FOREIGN KEY FK_60BA39DD115F0EE5');
        $this->addSql('ALTER TABLE project__domain DROP FOREIGN KEY FK_C884F5ED166D1F9C');
        $this->addSql('ALTER TABLE project__users DROP FOREIGN KEY FK_891FA23F166D1F9C');
        $this->addSql('ALTER TABLE project__users DROP FOREIGN KEY FK_891FA23FA76ED395');
        $this->addSql('ALTER TABLE asset__translation DROP FOREIGN KEY FK_9383376C5DA1941');
        $this->addSql('DROP TABLE asset__translation');
        $this->addSql('DROP TABLE locale__locale');
        $this->addSql('DROP TABLE project__domain');
        $this->addSql('DROP TABLE project__domain_locales');
        $this->addSql('DROP TABLE project__project');
        $this->addSql('DROP TABLE project__users');
        $this->addSql('DROP TABLE user__user');
        $this->addSql('DROP TABLE asset__asset');
    }
}
