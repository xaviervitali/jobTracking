<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240927165847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job ADD job_source_id INT DEFAULT NULL, ADD source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8528CF370 FOREIGN KEY (job_source_id) REFERENCES job_source (id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8953C1C61 FOREIGN KEY (source_id) REFERENCES job_source (id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8528CF370 ON job (job_source_id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8953C1C61 ON job (source_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8528CF370');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8953C1C61');
        $this->addSql('DROP INDEX IDX_FBD8E0F8528CF370 ON job');
        $this->addSql('DROP INDEX IDX_FBD8E0F8953C1C61 ON job');
        $this->addSql('ALTER TABLE job DROP job_source_id, DROP source_id');
    }
}
