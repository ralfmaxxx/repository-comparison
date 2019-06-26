<?php

declare(strict_types=1);

namespace App\Application\Http\Url;

final class Url
{
    private $scheme;
    private $host;
    private $firstSegment;
    private $secondSegment;
    private $thirdSegment;

    public function __construct(
        string $scheme,
        string $host,
        string $firstSegment,
        string $secondSegment,
        string $thirdSegment
    ) {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->firstSegment = $firstSegment;
        $this->secondSegment = $secondSegment;
        $this->thirdSegment = $thirdSegment;
    }

    public function containsNotEmptyData() : bool
    {
        return !empty($this->scheme) && !empty($this->host) && $this->hasOnlyFirstTwoNotEmptySegments();
    }

    public function isSecuredHost(string $host): bool
    {
        return $this->scheme === 'https' && $this->host === $host;
    }

    public function hasOnlyFirstTwoNotEmptySegments(): bool
    {
        return !empty($this->firstSegment) && !empty($this->secondSegment) && empty($this->thirdSegment);
    }

    public function hasNoHostAndScheme(): bool
    {
        return empty($this->host) && empty($this->scheme);
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
