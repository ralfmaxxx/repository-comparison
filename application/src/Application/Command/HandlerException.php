<?php

declare(strict_types=1);

namespace App\Application\Command;

use Exception;

final class HandlerException extends Exception
{
    public static function createFromPrevious(Exception $exception): self
    {
        return new self($exception->getMessage(), $exception->getCode(), $exception);
    }
}
