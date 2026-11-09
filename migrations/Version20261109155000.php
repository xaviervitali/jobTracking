<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use RuntimeException;

final class Version20261109155000 extends AbstractMigration
{
    private const TABLES = [
        'action' => 'jt_action',
        'address_book' => 'jt_address_book',
        'city' => 'jt_city',
        'cv' => 'jt_cv',
        'failed_login_attempt' => 'jt_failed_login_attempt',
        'job' => 'jt_job',
        'job_api_services' => 'jt_job_api_services',
        'job_api_services_job_search_settings' => 'jt_job_api_services_job_search_settings',
        'job_search_settings' => 'jt_job_search_settings',
        'job_source' => 'jt_job_source',
        'job_tracking' => 'jt_job_tracking',
        'messenger_messages' => 'jt_messenger_messages',
        'note' => 'jt_note',
        'user' => 'jt_user',
    ];

    public function getDescription(): string
    {
        return 'Prefix application tables with jt_.';
    }

    public function up(Schema $schema): void
    {
        $this->renameTables(self::TABLES);
    }

    public function down(Schema $schema): void
    {
        $this->renameTables(array_flip(self::TABLES));
    }

    /**
     * @param array<string, string> $tableNames
     */
    private function renameTables(array $tableNames): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        foreach ($tableNames as $source => $target) {
            $existingTables = $schemaManager->listTableNames();
            $sourceExists = in_array($source, $existingTables, true);
            $targetExists = in_array($target, $existingTables, true);

            if ($sourceExists && $targetExists) {
                throw new RuntimeException(sprintf(
                    'Cannot rename table "%s" to "%s": both tables exist.',
                    $source,
                    $target,
                ));
            }

            if ($sourceExists) {
                $this->addSql(sprintf(
                    'RENAME TABLE %s TO %s',
                    $this->connection->getDatabasePlatform()->quoteIdentifier($source),
                    $this->connection->getDatabasePlatform()->quoteIdentifier($target),
                ));
            }
        }
    }
}
