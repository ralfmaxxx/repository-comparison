<?php

declare(strict_types=1);

namespace App\Application\Query\Model;

final class BasicData
{
    private $id;
    private $username;
    private $name;
    private $status;

    public function __construct(
        string $id,
        string $username,
        string $name,
        string $status
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->name = $name;
        $this->status = $status;
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getStatus(): string
    {
        return $this->status;
    }
}
