<?php

declare(strict_types=1);

namespace App\Application\Http\Url;

interface UrlParser
{
    public function parse(string $url): Url;
}
