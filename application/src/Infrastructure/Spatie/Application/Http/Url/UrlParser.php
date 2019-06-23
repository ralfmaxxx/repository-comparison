<?php

declare(strict_types=1);

namespace App\Infrastructure\Spatie\Application\Http\Url;

use App\Application\Http\Url\Url;
use App\Application\Http\Url\UrlParser as BaseUrlParser;
use Spatie\Url\Url as SpatieUrl;

final class UrlParser implements BaseUrlParser
{
    public function parse(string $url): Url
    {
        $parsedUrl = SpatieUrl::fromString($url);

        return new Url($parsedUrl->getHost(), $parsedUrl->getFirstSegment() ?? '', $parsedUrl->getSegment(2) ?? '');
    }
}
