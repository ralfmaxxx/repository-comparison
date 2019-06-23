<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Dbal\Application\Query;

use App\Application\Query\ComparisonWithStatisticsQuery as ApplicationComparisonWithStatisticsQueryAlias;
use App\Application\Query\Model\Comparison;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ParameterType;

final class ComparisonWithStatisticsQuery implements ApplicationComparisonWithStatisticsQueryAlias
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findById(string $id): ?Comparison
    {
        $queryBuilder = $this
            ->connection
            ->createQueryBuilder()
            ->select('
                comparison.id AS comparison_id,
                first_repository_statistics.id AS first_repository_statistics_id,
                first_repository_statistics.username AS first_repository_statistics_username,
                first_repository_statistics.name AS first_repository_statistics_name,
                first_repository_statistics.status AS first_repository_statistics_status,
                first_repository_statistics.forks_count AS first_repository_statistics_forks_count,
                first_repository_statistics.stars_count AS first_repository_statistics_stars_count,
                first_repository_statistics.watchers_count AS first_repository_statistics_watchers_count,
                first_repository_statistics.last_release_date AS first_repository_statistics_last_release_date,
                first_repository_statistics.open_pr_count AS first_repository_statistics_open_pr_count,
                first_repository_statistics.closed_pr_count AS first_repository_statistics_closed_pr_count,
                second_repository_statistics.id AS second_repository_statistics_id,
                second_repository_statistics.username AS second_repository_statistics_username,
                second_repository_statistics.name AS second_repository_statistics_name,
                second_repository_statistics.status AS second_repository_statistics_status,
                second_repository_statistics.forks_count AS second_repository_statistics_forks_count,
                second_repository_statistics.stars_count AS second_repository_statistics_stars_count,
                second_repository_statistics.watchers_count AS second_repository_statistics_watchers_count,
                second_repository_statistics.last_release_date AS second_repository_statistics_last_release_date,
                second_repository_statistics.open_pr_count AS second_repository_statistics_open_pr_count,
                second_repository_statistics.closed_pr_count AS second_repository_statistics_closed_pr_count
            ')
            ->from('comparison')
            ->innerJoin(
                'comparison',
                'repository_statistics',
                'first_repository_statistics',
                'first_repository_statistics.id = comparison.first_statistics_id'
            )
            ->innerJoin(
                'comparison',
                'repository_statistics',
                'second_repository_statistics',
                'second_repository_statistics.id = comparison.second_statistics_id'
            )
            ->where('comparison.id = :comparisonId')
            ->setParameter(':comparisonId', $id, ParameterType::STRING);

        try {
            $result = $this->connection->fetchAssoc($queryBuilder->getSQL(), $queryBuilder->getParameters());
        } catch (DBALException $exception) {
            return null;
        }

        if ($result === false) {
            return null;
        }

        return Comparison::createFrom($result);
    }
}
