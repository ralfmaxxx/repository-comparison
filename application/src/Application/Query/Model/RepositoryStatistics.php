<?php

declare(strict_types=1);

namespace App\Application\Query\Model;

use DateTimeImmutable;

final class RepositoryStatistics
{
    private $basicData;
    private $forks;
    private $stars;
    private $watchers;
    private $lastReleaseDate;
    private $openPrs;
    private $closedPrs;

    public function __construct(
        BasicData $basicData,
        ?int $forks,
        ?int $stars,
        ?int $watchers,
        ?DateTimeImmutable $lastReleaseDate,
        ?int $openPrs,
        ?int $closedPrs
    ) {
        $this->basicData = $basicData;
        $this->forks = $forks;
        $this->stars = $stars;
        $this->watchers = $watchers;
        $this->lastReleaseDate = $lastReleaseDate;
        $this->openPrs = $openPrs;
        $this->closedPrs = $closedPrs;
    }

    public function getBasicData(): BasicData
    {
        return $this->basicData;
    }

    public function getForks(): ?int
    {
        return $this->forks;
    }

    public function getStars(): ?int
    {
        return $this->stars;
    }

    public function getWatchers(): ?int
    {
        return $this->watchers;
    }

    public function getLastReleaseDate(): ?DateTimeImmutable
    {
        return $this->lastReleaseDate;
    }

    public function hasLastReleaseDate(): bool
    {
        return !is_null($this->lastReleaseDate);
    }

    public function getOpenPrs(): ?int
    {
        return $this->openPrs;
    }

    public function getClosedPrs(): ?int
    {
        return $this->closedPrs;
    }
}
