<?php

declare(strict_types=1);

namespace App\Domain;

final class Comparison
{
    private $id;
    private $firstStatisticsId;
    private $secondStatisticsId;

    public function __construct(string $id, string $firstStatisticsId, string $secondStatisticsId)
    {
        $this->id = $id;
        $this->firstStatisticsId = $firstStatisticsId;
        $this->secondStatisticsId = $secondStatisticsId;
    }
}
