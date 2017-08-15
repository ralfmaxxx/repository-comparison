<?php

declare(strict_types=1);

namespace App\Application\Command;

interface Handler
{
    /**
     * @throws HandlerException
     */
    public function handle(Command $command) : void;
}
