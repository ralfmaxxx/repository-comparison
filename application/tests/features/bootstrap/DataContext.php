<?php

declare(strict_types=1);

namespace tests\features\bootstrap;

use App\Domain\Comparison;
use App\Domain\RepositoryStatistics;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use Symfony\Component\HttpKernel\KernelInterface;

final class DataContext implements Context
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Transform table:id,username,name,status,forks_count,stars_count,watchers_count,last_release_date,open_pr_count,closed_pr_count
     *
     * @return RepositoryStatistics[]
     *
     * @todo domain should not be used here
     */
    public function castRepositoryStatisticsCollection(TableNode $repositoryStatisticsCollection): array
    {
        $statisticsCollection = [];

        foreach ($repositoryStatisticsCollection as $repositoryStatistics) {
            $statistics = new RepositoryStatistics(
                $repositoryStatistics['id'],
                $repositoryStatistics['username'],
                $repositoryStatistics['name']
            );
            $statistics->addStatistics(
                (int) $repositoryStatistics['forks_count'],
                (int) $repositoryStatistics['stars_count'],
                (int) $repositoryStatistics['watchers_count'],
                new DateTimeImmutable($repositoryStatistics['last_release_date']),
                (int) $repositoryStatistics['open_pr_count'],
                (int) $repositoryStatistics['closed_pr_count']
            );

            $statisticsCollection[] = $statistics;

        }

        return $statisticsCollection;
    }

    /**
     * @Transform table:id,first_statistics_id,second_statistics_id
     *
     * @return Comparison[]
     *
     * @todo domain should not be used here
     */
    public function castComparisons(TableNode $comparisons): array
    {
        return array_map(
            function (array $comparison): Comparison
            {
                return new Comparison(
                    $comparison['id'],
                    $comparison['first_statistics_id'],
                    $comparison['second_statistics_id']
                );
            },
            $comparisons->getHash()
        );
    }

    /**
     * @Given there are repository statistics:
     *
     * @param RepositoryStatistics[] $repositoryStatisticsCollection
     */
    public function thereAreRepositoryStatistics(array $repositoryStatisticsCollection): void
    {
        $repository = $this->kernel->getContainer()->get('app.infrastructure.doctrine.repository.repository_statistics');

        foreach ($repositoryStatisticsCollection as $repositoryStatistics) {
            $repository->add($repositoryStatistics);
        }

        $this->kernel->getContainer()->get('doctrine.orm.entity_manager')->flush();
    }

    /**
     * @Given there are comparisons:
     *
     * @param Comparison[] $comparisons
     */
    public function thereAreComparisons(array $comparisons): void
    {
        $repository = $this->kernel->getContainer()->get('app.infrastructure.doctrine.repository.comparisons');

        foreach ($comparisons as $comparison) {
            $repository->add($comparison);
        }

        $this->kernel->getContainer()->get('doctrine.orm.entity_manager')->flush();
    }
}
