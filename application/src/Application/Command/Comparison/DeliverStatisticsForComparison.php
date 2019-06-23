<?php

declare(strict_types=1);

namespace App\Application\Command\Comparison;

use App\Application\Command\Command;

final class DeliverStatisticsForComparison implements Command
{
    private $comparisonId;

    public function __construct(string $comparisonId)
    {
        $this->comparisonId = $comparisonId;
    }

    public function getComparisonId(): string
    {
        return $this->comparisonId;
    }
}
