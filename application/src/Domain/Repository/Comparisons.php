<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Comparison;

interface Comparisons
{
    /**
     * @throws CouldNotAddException
     */
    public function add(Comparison $comparison): void;

    public function findOneById(string $id): ?Comparison;
}
