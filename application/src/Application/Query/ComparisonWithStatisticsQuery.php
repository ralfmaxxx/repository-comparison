<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\Query\Model\Comparison;

interface ComparisonWithStatisticsQuery
{
    public function findById(string $id): ?Comparison;
}
