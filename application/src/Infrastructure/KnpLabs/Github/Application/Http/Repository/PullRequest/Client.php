<?php

declare(strict_types=1);

namespace App\Infrastructure\KnpLabs\Github\Application\Http\Repository\PullRequest;

use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\PullRequests;
use Github\Client as GithubClient;
use Github\Exception\ExceptionInterface;

class Client
{
    private const ALL_STATE = 'all';
    private const OPEN_STATE = 'open';
    private const CLOSED_STATE = 'closed';

    private $client;

    public function __construct(GithubClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throws ClientException
     */
    public function get(Repository $repository): PullRequests
    {
        $page = 1;

        $statistics = [
            'open' => 0,
            'closed' => 0,
        ];

        while ($pullRequests = $this->getPullRequests($repository, $page++)) {
            $this->countStatistics($pullRequests, $statistics);
        }

        return new PullRequests($statistics['open'], $statistics['closed']);
    }

    private function getPullRequests(Repository $repository, int $page): array
    {
        try {
            return $this->client->pullRequests()->all(
                $repository->getUsername(),
                $repository->getName(),
                ['state' => self::ALL_STATE, 'page' => $page]
            );
        } catch (ExceptionInterface $exception) {
            throw ClientException::createFromPrevious($exception->getMessage(), $exception);
        }
    }

    private function countStatistics(array $pullRequests, array &$statistics): void
    {
        foreach ($pullRequests as $pullRequest) {
            if (($pullRequest['state'] ?? '') === self::OPEN_STATE) {
                $statistics['open']++;

                continue;
            }

            if (($pullRequest['state'] ?? '') === self::CLOSED_STATE) {
                $statistics['closed']++;

                continue;
            }
        }
    }
}
