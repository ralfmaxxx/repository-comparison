<?php

declare(strict_types=1);

namespace App\UserInterface\Symfony\Controller;

use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\Response;

final class DefaultController
{
    private const EMPTY_RESPONSE = '';

    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function execute(): Response
    {
        return new Response(self::EMPTY_RESPONSE);
    }
}
