<?php

declare(strict_types=1);

namespace App\Application\Http\Repository\Exception;

use Exception;
use RuntimeException;

final class ClientException extends RuntimeException
{
    public static function createFromPrevious(string $message, Exception $previous): self
    {
        return new self(
            $message,
            0,
            $previous
        );
    }
}
