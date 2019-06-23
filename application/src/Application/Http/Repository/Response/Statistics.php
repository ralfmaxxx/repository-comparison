<?php

declare(strict_types=1);

namespace App\Application\Http\Repository\Response;

final class Statistics
{
    private $forks;
    private $stars;
    private $watchers;

    public function __construct(int $forks, int $stars, int $watchers)
    {
        $this->forks = $forks;
        $this->stars = $stars;
        $this->watchers = $watchers;
    }

    public function forksCount(): int
    {
        return $this->forks;
    }

    public function starsCount(): int
    {
        return $this->stars;
    }

    public function watchersCount(): int
    {
        return $this->watchers;
    }
}
