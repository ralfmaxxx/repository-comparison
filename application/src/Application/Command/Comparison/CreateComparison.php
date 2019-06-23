<?php

declare(strict_types=1);

namespace App\Application\Command\Comparison;

use App\Application\Command\Command;

final class CreateComparison implements Command
{
    private $id;
    private $firstRepositoryName;
    private $secondRepositoryName;

    public function __construct(string $id, string $firstRepositoryName, string $secondRepositoryName)
    {
        $this->id = $id;
        $this->firstRepositoryName = $firstRepositoryName;
        $this->secondRepositoryName = $secondRepositoryName;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstRepositoryName(): string
    {
        return $this->firstRepositoryName;
    }

    public function getSecondRepositoryName(): string
    {
        return $this->secondRepositoryName;
    }
}
