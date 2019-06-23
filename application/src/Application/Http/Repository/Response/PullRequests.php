<?php

declare(strict_types=1);

namespace App\Application\Http\Repository\Response;

final class PullRequests
{
    private $open;
    private $closed;

    public function __construct(int $open, int $closed)
    {
        $this->open = $open;
        $this->closed = $closed;
    }

    public function openCount(): int
    {
        return $this->open;
    }

    public function closedCount(): int
    {
        return $this->closed;
    }
}
