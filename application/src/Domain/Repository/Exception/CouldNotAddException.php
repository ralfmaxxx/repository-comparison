<?php

declare(strict_types=1);

namespace App\Domain\Repository\Exception;

use Exception;
use RuntimeException;

final class CouldNotAddException extends RuntimeException
{
    public static function createFromPrevious(Exception $exception): self
    {
        return new self($exception->getMessage(), $exception->getCode(), $exception);
    }
}
