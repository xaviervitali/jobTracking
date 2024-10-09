<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241008221206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_search_settings ADD city_id INT DEFAULT NULL, DROP city');
        $this->addSql('ALTER TABLE job_search_settings ADD CONSTRAINT FK_1E8E441F8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_1E8E441F8BAC62AF ON job_search_settings (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_search_settings DROP FOREIGN KEY FK_1E8E441F8BAC62AF');
        $this->addSql('DROP INDEX IDX_1E8E441F8BAC62AF ON job_search_settings');
        $this->addSql('ALTER TABLE job_search_settings ADD city VARCHAR(255) NOT NULL, DROP city_id');
    }
}
