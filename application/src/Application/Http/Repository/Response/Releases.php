<?php

declare(strict_types=1);

namespace App\Application\Http\Repository\Response;

use DateTimeImmutable;

final class Releases
{
    private $lastReleaseDate;

    public function __construct(DateTimeImmutable $lastReleaseDate)
    {
        $this->lastReleaseDate = $lastReleaseDate;
    }

    public function getLastReleaseDate(): DateTimeImmutable
    {
        return $this->lastReleaseDate;
    }
}
