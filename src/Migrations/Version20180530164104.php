<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180530164104 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE locale__locale DROP FOREIGN KEY FK_E470A0B2115F0EE5');
        $this->addSql('DROP INDEX IDX_E470A0B2115F0EE5 ON locale__locale');
        $this->addSql('ALTER TABLE locale__locale DROP domain_id');
        $this->addSql('INSERT INTO `locale__locale` (`name`, `code`, `created_at`, `updated_at`) VALUES (\'Dansk\', \'da\', NOW(), NOW()), (\'Deutsch\', \'de\', NOW(), NOW()), (\'Ελληνικά\', \'el\', NOW(), NOW()), (\'English\', \'en\', NOW(), NOW()), (\'Español\', \'es\', NOW(), NOW()), (\'Euskara\', \'eu\', NOW(), NOW()), (\'Suomen kieli\', \'fi\', NOW(), NOW()), (\'Français\', \'fr\', NOW(), NOW()), (\'Italiano\', \'it\', NOW(), NOW()), (\'Lëtzebuergesch\', \'lb\', NOW(), NOW()), (\'Nederlands\', \'nl\', NOW(), NOW()), (\'Norsk\', \'no\', NOW(), NOW()), (\'Português\', \'pt\', NOW(), NOW()), (\'русский язык\', \'ru\', NOW(), NOW()), (\'Svenska\', \'sv\', NOW(), NOW())');

        $this->addSql('CREATE TABLE project__domain_locales (domain_id INT NOT NULL, locale_id INT NOT NULL, INDEX IDX_825DFA89115F0EE5 (domain_id), INDEX IDX_825DFA89E559DFD1 (locale_id), PRIMARY KEY(domain_id, locale_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project__domain_locales ADD CONSTRAINT FK_825DFA89115F0EE5 FOREIGN KEY (domain_id) REFERENCES project__domain (id)');
        $this->addSql('ALTER TABLE project__domain_locales ADD CONSTRAINT FK_825DFA89E559DFD1 FOREIGN KEY (locale_id) REFERENCES locale__locale (id)');
        $this->addSql('ALTER TABLE project__domain DROP FOREIGN KEY FK_C884F5ED743BF776');
        $this->addSql('DROP INDEX IDX_C884F5ED743BF776 ON project__domain');
        $this->addSql('ALTER TABLE project__domain DROP default_locale_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE project__domain_locales');
        $this->addSql('ALTER TABLE project__domain ADD default_locale_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project__domain ADD CONSTRAINT FK_C884F5ED743BF776 FOREIGN KEY (default_locale_id) REFERENCES locale__locale (id)');
        $this->addSql('CREATE INDEX IDX_C884F5ED743BF776 ON project__domain (default_locale_id)');

        $this->addSql('ALTER TABLE locale__locale ADD domain_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE locale__locale ADD CONSTRAINT FK_E470A0B2115F0EE5 FOREIGN KEY (domain_id) REFERENCES project__domain (id)');
        $this->addSql('CREATE INDEX IDX_E470A0B2115F0EE5 ON locale__locale (domain_id)');
    }
}
