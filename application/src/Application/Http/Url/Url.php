<?php

declare(strict_types=1);

namespace App\Application\Http\Url;

final class Url
{
    private $host;
    private $firstSegment;
    private $secondSegment;

    public function __construct(string $host, string $firstSegment, string $secondSegment)
    {
        $this->host = $host;
        $this->firstSegment = $firstSegment;
        $this->secondSegment = $secondSegment;
    }

    public function getFirstSegment(): string
    {
        return $this->firstSegment;
    }

    public function getSecondSegment(): string
    {
        return $this->secondSegment;
    }
}
