<?php

declare(strict_types=1);

namespace App\Application\Command\Comparison;

use App\Application\Command\Command;
use App\Application\Command\Handler;
use App\Application\Command\HandlerException;
use App\Application\Http\Repository\Client as HttpRepositoryClient;
use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Transaction;
use App\Domain\Repository\Comparisons;
use App\Domain\Repository\RepositoryStatistics;

final class DeliverStatisticsForComparisonHandler implements Handler
{
    private const COMPARISON_NOT_FOUND_MESSAGE = 'Comparison not found';

    private $transaction;
    private $client;
    private $comparisons;
    private $repositoryStatistics;

    public function __construct(
        Transaction $transaction,
        HttpRepositoryClient $client,
        Comparisons $comparisons,
        RepositoryStatistics $repositoryStatistics
    ) {
        $this->transaction = $transaction;
        $this->client = $client;
        $this->comparisons = $comparisons;
        $this->repositoryStatistics = $repositoryStatistics;
    }

    /**
     * @param Command|DeliverStatisticsForComparison $command
     *
     * @throws HandlerException
     */
    public function handle(Command $command): void
    {
        $this->transaction->begin();

        $comparison = $this->comparisons->findOneById($command->getComparisonId());

        if (is_null($comparison)) {
            $this->transaction->rollback();

            throw new HandlerException(self::COMPARISON_NOT_FOUND_MESSAGE);
        }

        $firstRepositoryStatistics = $this
            ->repositoryStatistics
            ->findOneById($comparison->getFirstStatisticsId());
        $secondRepositoryStatistics = $this
            ->repositoryStatistics
            ->findOneById($comparison->getSecondStatisticsId());

        if (is_null($firstRepositoryStatistics) || is_null($secondRepositoryStatistics)) {
            $this->transaction->rollback();

            throw new HandlerException(self::COMPARISON_NOT_FOUND_MESSAGE);
        }

        try {
            $statisticsForFirstRepository = $this->client->getInformation(
                new Repository($firstRepositoryStatistics->getUsername(), $firstRepositoryStatistics->getName())
            );
            $statisticsForSecondRepository = $this->client->getInformation(
                new Repository($secondRepositoryStatistics->getUsername(), $secondRepositoryStatistics->getName())
            );
        } catch (ClientException $exception) {
            $this->transaction->rollback();

            throw HandlerException::createFromPrevious($exception);
        }

        $firstRepositoryStatistics->addStatistics(
            $statisticsForFirstRepository->getStatistics()->forksCount(),
            $statisticsForFirstRepository->getStatistics()->starsCount(),
            $statisticsForFirstRepository->getStatistics()->watchersCount(),
            $statisticsForFirstRepository->getReleases()->getLastReleaseDate(),
            $statisticsForFirstRepository->getPullRequests()->openCount(),
            $statisticsForFirstRepository->getPullRequests()->closedCount()
        );

        $secondRepositoryStatistics->addStatistics(
            $statisticsForSecondRepository->getStatistics()->forksCount(),
            $statisticsForSecondRepository->getStatistics()->starsCount(),
            $statisticsForSecondRepository->getStatistics()->watchersCount(),
            $statisticsForSecondRepository->getReleases()->getLastReleaseDate(),
            $statisticsForSecondRepository->getPullRequests()->openCount(),
            $statisticsForSecondRepository->getPullRequests()->closedCount()
        );

        $this->transaction->commit();
    }
}
