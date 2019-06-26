<?php

declare(strict_types=1);

namespace App\Infrastructure\KnpLabs\Github\Application\Http\Response\Counter;

use Github\HttpClient\Message\ResponseMediator;
use Psr\Http\Message\ResponseInterface;

class PageCounter
{
    public function count(ResponseInterface $response): int
    {
        $resources = ResponseMediator::getContent($response);

        if (!is_array($resources) || count($resources) === 0) {
            return 0;
        }

        $pages = ResponseMediator::getPagination($response);

        if (!is_array($pages) || !isset($pages['last'])) {
            return 1;
        }

        parse_str(parse_url($pages['last'], PHP_URL_QUERY), $params);

        return isset($params['page']) ? (int) $params['page'] : 1;
    }
}
