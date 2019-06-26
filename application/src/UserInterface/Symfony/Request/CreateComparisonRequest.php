<?php

declare(strict_types=1);

namespace App\UserInterface\Symfony\Request;

use ArrayIterator;
use IteratorAggregate;
use Symfony\Component\HttpFoundation\Request;
use Traversable;

final class CreateComparisonRequest implements IteratorAggregate
{
    private const FIRST_REPOSITORY_FIELD_NAME = 'firstRepository';
    private const SECOND_REPOSITORY_FIELD_NAME = 'secondRepository';

    private $firstRepositoryName;
    private $secondRepositoryName;

    public function __construct(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $this->firstRepositoryName = trim((string) ($data[self::FIRST_REPOSITORY_FIELD_NAME] ?? ''));
        $this->secondRepositoryName = trim((string) ($data[self::SECOND_REPOSITORY_FIELD_NAME] ?? ''));
    }

    public function getFirstRepositoryName(): string
    {
        return $this->firstRepositoryName;
    }

    public function getSecondRepositoryName(): string
    {
        return $this->secondRepositoryName;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator(
            [
                self::FIRST_REPOSITORY_FIELD_NAME => $this->firstRepositoryName,
                self::SECOND_REPOSITORY_FIELD_NAME => $this->secondRepositoryName,
            ]
        );
    }
}
