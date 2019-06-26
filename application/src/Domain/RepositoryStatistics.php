<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

final class RepositoryStatistics
{
    private const STATUS_PENDING = 'pending';
    private const STATUS_DELIVERED = 'delivered';

    private $id;
    private $username;
    private $name;
    private $status;
    private $forksCount;
    private $starsCount;
    private $watchersCount;
    private $lastReleaseDate;
    private $openPRCount;
    private $closedPRCount;

    public function __construct(
        string $id,
        string $username,
        string $name
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->name = $name;
        $this->status = self::STATUS_PENDING;
        $this->forksCount = null;
        $this->starsCount = null;
        $this->watchersCount = null;
        $this->lastReleaseDate = null;
        $this->openPRCount = null;
        $this->closedPRCount = null;
    }

    public function addStatistics(
        int $forksCount,
        int $starsCount,
        int $watchersCount,
        ?DateTimeImmutable $lastReleaseDate,
        int $openPRCount,
        int $closedPRCount
    ): void {
        $this->status = self::STATUS_DELIVERED;

        $this->forksCount = $forksCount;
        $this->starsCount = $starsCount;
        $this->watchersCount = $watchersCount;
        $this->lastReleaseDate = $lastReleaseDate;
        $this->openPRCount = $openPRCount;
        $this->closedPRCount = $closedPRCount;
    }
}
