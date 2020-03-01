<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Repository\Exception\CouldNotAddException;
use App\Domain\RepositoryStatistics as Statistics;

interface RepositoryStatistics
{
    /**
     * @throws CouldNotAddException
     */
    public function add(Statistics $repositoryStatistics): void;

    public function findOneById(string $id): ?Statistics;
}
