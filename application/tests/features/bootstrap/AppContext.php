<?php

namespace tests\features\bootstrap;

use App\Domain\Comparison;
use App\Domain\RepositoryStatistics;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Exception;
use Mockery;
use PHPUnit\Framework\Assert;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class AppContext implements Context
{
    private $kernel;

    /**
     * @var Response|null
     */
    private $response;

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
        $statisticsColllection = [];

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

            $statisticsColllection[] = $statistics;

        }

        return $statisticsColllection;
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
     * @BeforeScenario
     *
     * @throws Exception
     */
    public function clearDatabase(): void
    {
        $entityManager = $this->kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($entityManager);
        $executor = new ORMExecutor($entityManager, $purger);

        $executor->purge();
        $entityManager->clear();
    }

    /**
     * @AfterScenario
     */
    public function closeMockery(): void
    {
        Mockery::close();
    }

    /**
     * @When I send :type request to :path
     */
    public function iSendRequestTo(string $type, string $path): void
    {
        $this->response = $this->kernel->handle(Request::create($path, $type));
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

    /**
     * @Then the response status should be :status
     */
    public function theResponseStatusShouldBe(int $status): void
    {
        if ($this->response === null) {
            throw new RuntimeException('No response received');
        }

        Assert::assertEquals($status, $this->response->getStatusCode());
    }

    /**
     * @Then the response should be:
     */
    public function theResponseShouldBe(PyStringNode $string): void
    {
        if ($this->response === null) {
            throw new RuntimeException('No response received');
        }

        Assert::assertEquals(
            json_decode($string->getRaw(), true),
            json_decode($this->response->getContent(), true)
        );
    }
}
