<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM\Domain\Repository;

use App\Domain\Repository\Exception\CouldNotAddException;
use App\Domain\Repository\RepositoryStatistics as RepositoryStatisticsRepository;
use App\Domain\RepositoryStatistics as Statistics;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;

final class RepositoryStatistics implements RepositoryStatisticsRepository
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws CouldNotAddException
     */
    public function add(Statistics $repositoryStatistics): void
    {
        try {
            $this->entityManager->persist($repositoryStatistics);
        } catch (ORMException|ORMInvalidArgumentException $exception) {
            throw CouldNotAddException::createFromPrevious($exception);
        }
    }

    public function findOneById(string $id): ?Statistics
    {
        return $this->entityManager->getRepository(Statistics::class)->findOneBy(['id' => $id]);
    }
}
