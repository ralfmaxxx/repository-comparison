<?php

declare(strict_types=1);

namespace App\Infrastructure\KnpLabs\Github\Application\Http\Repository;

use App\Application\Http\Repository\Client as ApplicationClient;
use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\Information;
use App\Infrastructure\KnpLabs\Github\Application\Http\Repository\PullRequest\Client as PullRequestClient;
use App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Release\Client as ReleaseClient;
use App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Statistics\Client as StatisticsClient;

final class Client implements ApplicationClient
{
    private $statisticsClient;
    private $releaseClient;
    private $pullRequestClient;

    public function __construct(
        StatisticsClient $statisticsClient,
        ReleaseClient $releaseClient,
        PullRequestClient $pullRequestClient
    ) {
        $this->statisticsClient = $statisticsClient;
        $this->releaseClient = $releaseClient;
        $this->pullRequestClient = $pullRequestClient;
    }

    /**
     * @throws ClientException
     */
    public function getInformation(Repository $repository): Information
    {
        return new Information(
            $this->statisticsClient->get($repository),
            $this->releaseClient->get($repository),
            $this->pullRequestClient->get($repository)
        );
    }
}
