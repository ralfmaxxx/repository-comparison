<?php

declare(strict_types=1);

namespace App\Application\Http\Repository\Request;

final class Repository
{
    private $username;
    private $name;

    public function __construct(string $username, string $name)
    {
        $this->username = $username;
        $this->name = $name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFullName(): string
    {
        return sprintf('%s/%s', $this->username, $this->name);
    }
}
