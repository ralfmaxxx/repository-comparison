<?php

declare(strict_types=1);

namespace App\UserInterface\Symfony\Controller;

use Symfony\Component\HttpFoundation\Response;

final class DefaultController
{
    private const INFORMATION = 'This project delivers only API methods.';

    public function execute(): Response
    {
        return new Response(self::INFORMATION);
    }
}
