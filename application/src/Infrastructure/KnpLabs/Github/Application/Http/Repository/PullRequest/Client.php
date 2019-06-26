<?php

declare(strict_types=1);

namespace App\Infrastructure\KnpLabs\Github\Application\Http\Repository\PullRequest;

use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\PullRequests;
use App\Infrastructure\KnpLabs\Github\Application\Http\Response\Counter\PageCounter;
use Github\Client as GithubClient;
use Github\Exception\ExceptionInterface;

class Client
{
    private const OPEN_STATE = 'open';
    private const CLOSED_STATE = 'closed';

    private $client;
    private $pageCounter;

    public function __construct(
        GithubClient $client,
        PageCounter $pageCounter
    ) {
        $this->client = $client;
        $this->pageCounter = $pageCounter;
    }

    /**
     * @throws ClientException
     */
    public function get(Repository $repository): PullRequests
    {
        try {
            $this->client->pullRequests()->all(
                $repository->getUsername(),
                $repository->getName(),
                ['state' => self::OPEN_STATE, 'page' => 1, 'per_page' => 1]
            );

            $openPullRequestsCount = $this->pageCounter->count($this->client->getLastResponse());

            $this->client->pullRequests()->all(
                $repository->getUsername(),
                $repository->getName(),
                ['state' => self::CLOSED_STATE, 'page' => 1, 'per_page' => 1]
            );

            $closedPullRequestsCount = $this->pageCounter->count($this->client->getLastResponse());
        } catch (ExceptionInterface $exception) {
            throw ClientException::createFromPrevious(
                sprintf('Error "%s" - for repository %s', $exception->getMessage(), $repository->getFullName()),
                $exception
            );
        }

        return new PullRequests($openPullRequestsCount, $closedPullRequestsCount);
    }
}
