<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180606083943 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project__domain_locales DROP FOREIGN KEY FK_825DFA89115F0EE5');
        $this->addSql('ALTER TABLE project__domain_locales DROP FOREIGN KEY FK_825DFA89E559DFD1');
        $this->addSql('DROP INDEX idx_825dfa89115f0ee5 ON project__domain_locales');
        $this->addSql('CREATE INDEX IDX_E30465DC115F0EE5 ON project__domain_locales (domain_id)');
        $this->addSql('DROP INDEX idx_825dfa89e559dfd1 ON project__domain_locales');
        $this->addSql('CREATE INDEX IDX_E30465DCE559DFD1 ON project__domain_locales (locale_id)');
        $this->addSql('ALTER TABLE project__domain_locales ADD CONSTRAINT FK_825DFA89115F0EE5 FOREIGN KEY (domain_id) REFERENCES project__domain (id)');
        $this->addSql('ALTER TABLE project__domain_locales ADD CONSTRAINT FK_825DFA89E559DFD1 FOREIGN KEY (locale_id) REFERENCES locale__locale (id)');
        $this->addSql('ALTER TABLE asset__asset CHANGE source source LONGTEXT NOT NULL, resname resname VARCHAR(190) NOT NULL');
        $this->addSql('CREATE INDEX resname_idx ON asset__asset (resname)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX resname_idx ON asset__asset');
        $this->addSql('ALTER TABLE asset__asset CHANGE source source VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, resname resname VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE project__domain_locales DROP FOREIGN KEY FK_E30465DC115F0EE5');
        $this->addSql('ALTER TABLE project__domain_locales DROP FOREIGN KEY FK_E30465DCE559DFD1');
        $this->addSql('DROP INDEX idx_e30465dc115f0ee5 ON project__domain_locales');
        $this->addSql('CREATE INDEX IDX_825DFA89115F0EE5 ON project__domain_locales (domain_id)');
        $this->addSql('DROP INDEX idx_e30465dce559dfd1 ON project__domain_locales');
        $this->addSql('CREATE INDEX IDX_825DFA89E559DFD1 ON project__domain_locales (locale_id)');
        $this->addSql('ALTER TABLE project__domain_locales ADD CONSTRAINT FK_E30465DC115F0EE5 FOREIGN KEY (domain_id) REFERENCES project__domain (id)');
        $this->addSql('ALTER TABLE project__domain_locales ADD CONSTRAINT FK_E30465DCE559DFD1 FOREIGN KEY (locale_id) REFERENCES locale__locale (id)');
    }
}
