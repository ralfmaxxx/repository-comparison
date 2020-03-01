<?php

declare(strict_types=1);

namespace App\Application\Command\Comparison;

use App\Application\Command\Command;
use App\Application\Command\Handler;
use App\Application\Command\HandlerException;
use App\Application\Http\Url\UrlParser;
use App\Application\Transaction;
use App\Domain\Comparison;
use App\Domain\Repository\Comparisons;
use App\Domain\Repository\Exception\CouldNotAddException;
use App\Domain\Repository\RepositoryStatistics;
use App\Domain\RepositoryStatistics as Statistics;
use Ramsey\Uuid\Uuid;

final class CreateComparisonHandler implements Handler
{
    private $transaction;
    private $comparisons;
    private $repositoryStatistics;
    private $urlParser;

    public function __construct(
        Transaction $transaction,
        Comparisons $comparisons,
        RepositoryStatistics $repositoryStatistics,
        UrlParser $urlParser
    ) {
        $this->transaction = $transaction;
        $this->comparisons = $comparisons;
        $this->repositoryStatistics = $repositoryStatistics;
        $this->urlParser = $urlParser;
    }

    /**
     * @param CreateComparison|Command $command
     *
     * @throws HandlerException
     */
    public function handle(Command $command): void
    {
        $firstRepositoryUrl = $this->urlParser->parse($command->getFirstRepositoryName());
        $secondRepositoryUrl = $this->urlParser->parse($command->getSecondRepositoryName());

        $this->transaction->begin();

        $firstStatisticsId = Uuid::uuid4()->toString();
        $secondStatisticsId = Uuid::uuid4()->toString();

        $firstRepositoryStatistics = new Statistics(
            $firstStatisticsId,
            $firstRepositoryUrl->getFirstSegment(),
            $firstRepositoryUrl->getSecondSegment()
        );
        $secondRepositoryStatistics = new Statistics(
            $secondStatisticsId,
            $secondRepositoryUrl->getFirstSegment(),
            $secondRepositoryUrl->getSecondSegment()
        );

        try {
            $this->repositoryStatistics->add($firstRepositoryStatistics);
            $this->repositoryStatistics->add($secondRepositoryStatistics);
            $this->comparisons->add(new Comparison($command->getId(), $firstStatisticsId, $secondStatisticsId));
        } catch (CouldNotAddException $exception) {
            $this->transaction->rollback();

            throw HandlerException::createFromPrevious($exception);
        }

        $this->transaction->commit();
    }
}
