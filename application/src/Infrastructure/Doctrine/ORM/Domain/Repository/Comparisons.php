<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM\Domain\Repository;

use App\Domain\Comparison;
use App\Domain\Repository\Comparisons as ComparisonRepository;
use App\Domain\Repository\CouldNotAddException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;

final class Comparisons implements ComparisonRepository
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws CouldNotAddException
     */
    public function add(Comparison $comparison): void
    {
        try {
            $this->entityManager->persist($comparison);
        } catch (ORMException|ORMInvalidArgumentException $exception) {
            throw CouldNotAddException::createFromPrevious($exception);
        }
    }

    public function findOneById(string $id): ?Comparison
    {
        return $this->entityManager->getRepository(Comparison::class)->findOneBy(['id' => $id]);
    }
}
