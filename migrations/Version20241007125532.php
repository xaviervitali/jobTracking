<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241007125532 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adzuna_api_settings ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE adzuna_api_settings ADD CONSTRAINT FK_A46CCED0A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A46CCED0A76ED395 ON adzuna_api_settings (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adzuna_api_settings DROP FOREIGN KEY FK_A46CCED0A76ED395');
        $this->addSql('DROP INDEX UNIQ_A46CCED0A76ED395 ON adzuna_api_settings');
        $this->addSql('ALTER TABLE adzuna_api_settings DROP user_id');
    }
}
