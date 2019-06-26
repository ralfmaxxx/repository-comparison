<?php

declare(strict_types=1);

namespace App\Application\Http\Repository\Response;

use DateTimeImmutable;

final class Releases
{
    private $lastReleaseDate;

    private function __construct(?DateTimeImmutable $lastReleaseDate)
    {
        $this->lastReleaseDate = $lastReleaseDate;
    }

    public static function createWithLastReleaseDate(DateTimeImmutable $lastReleaseDate): self
    {
        return new self($lastReleaseDate);
    }

    public static function createWithoutLastRelease(): self
    {
        return new self(null);
    }

    public function getLastReleaseDate(): ?DateTimeImmutable
    {
        return $this->lastReleaseDate;
    }
}
