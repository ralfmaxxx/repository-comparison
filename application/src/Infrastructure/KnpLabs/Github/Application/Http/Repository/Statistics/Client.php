<?php

declare(strict_types=1);

namespace App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Statistics;

use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\Statistics;
use Github\Client as GithubClient;
use Github\Exception\ExceptionInterface;

class Client
{
    private const MISSING_STATISTICS_ERROR = 'Statistics for repository "%s" does not have all needed data.';

    private $client;

    public function __construct(GithubClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throws ClientException
     */
    public function get(Repository $repository): Statistics
    {
        try {
            $statistics = $this->client->repos()->show($repository->getUsername(), $repository->getName());
        } catch (ExceptionInterface $exception) {
            throw ClientException::createFromPrevious(
                sprintf('"%s" - for repository %s', $exception->getMessage(), $repository->getFullName()),
                $exception
            );
        }

        if (!isset($statistics['forks_count'], $statistics['stargazers_count'], $statistics['subscribers_count'])) {
            throw new ClientException(
                sprintf(self::MISSING_STATISTICS_ERROR, $repository->getFullName())
            );
        }

        return new Statistics(
            (int) $statistics['forks_count'],
            (int) $statistics['stargazers_count'],
            (int) $statistics['subscribers_count']
        );
    }
}
