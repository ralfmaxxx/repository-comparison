<?php

declare(strict_types=1);

namespace App\Application\Http\Repository\Response;

final class Information
{
    private $statistics;
    private $releases;
    private $pullRequests;

    public function __construct(Statistics $statistics, Releases $releases, PullRequests $pullRequests)
    {
        $this->statistics = $statistics;
        $this->releases = $releases;
        $this->pullRequests = $pullRequests;
    }

    public function getStatistics(): Statistics
    {
        return $this->statistics;
    }

    public function getReleases(): Releases
    {
        return $this->releases;
    }

    public function getPullRequests(): PullRequests
    {
        return $this->pullRequests;
    }
}
