<?php

declare(strict_types=1);

namespace migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190623135312 extends AbstractMigration
{
    private const DESCRIPTION = 'Comparison and repository statistics';

    private const CREATE_COMPARISON_TABLE_SQL = '
        CREATE TABLE comparison (
            id VARCHAR(255) NOT NULL,
            first_statistics_id VARCHAR(100) NOT NULL,
            second_statistics_id VARCHAR(100) NOT NULL,
            INDEX efficient_search_by_first_statistics_index
                (first_statistics_id),
            INDEX efficient_search_by_second_statistics_index
                (second_statistics_id),
            PRIMARY KEY(id)
        )
        DEFAULT CHARACTER SET utf8mb4
        COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
    ';
    private const CREATE_REPOSITORY_STATISTICS_TABLE_SQL = '
        CREATE TABLE repository_statistics(
            id VARCHAR(255) NOT NULL,
            username VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            status VARCHAR(50) NOT NULL,
            forks_count INT DEFAULT NULL,
            stars_count INT DEFAULT NULL,
            watchers_count INT DEFAULT NULL,
            last_release_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            open_pr_count INT DEFAULT NULL,
            closed_pr_count INT DEFAULT NULL,
            INDEX efficient_search_by_status_index (status),
            INDEX efficient_search_by_name_index (name),
            PRIMARY KEY(id)
        )
        DEFAULT CHARACTER SET utf8mb4
        COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
    ';

    private const REMOVE_COMPARISON_TABLE_SQL = 'DROP TABLE comparison';
    private const REMOVE_REPOSITORY_STATISTICS_TABLE_SQL = 'DROP TABLE repository_statistics';

    public function getDescription(): string
    {
        return self::DESCRIPTION;
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(self::CREATE_REPOSITORY_STATISTICS_TABLE_SQL);
        $this->addSql(self::CREATE_COMPARISON_TABLE_SQL);
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(self::REMOVE_REPOSITORY_STATISTICS_TABLE_SQL);
        $this->addSql(self::REMOVE_COMPARISON_TABLE_SQL);
    }
}
