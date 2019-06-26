<?php

declare(strict_types=1);

namespace App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Release;

use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\Releases;
use App\Infrastructure\KnpLabs\Github\Application\Http\Response\Counter\PageCounter;
use DateTimeImmutable;
use Exception;
use Github\Client as GithubClient;
use Github\Exception\ExceptionInterface;

class Client
{
    private const MISSING_RELEASE_DATA_ERROR = 'Missing data about releases for repository "%s".';
    private const LAST_RELEASE_DATE_BAD_FORMAT_ERROR = 'Last release date is in bad format for repository "%s".';

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
    public function get(Repository $repository): Releases
    {
        try {
            $this->client->repo()->releases()
                ->all(
                    $repository->getUsername(),
                    $repository->getName(),
                    ['per_page' => 1, 'page' => 1]
                );

            if ($this->pageCounter->count($this->client->getLastResponse()) === 0) {
                return Releases::createWithoutLastRelease();
            }

            $lastRelease = $this->client->repo()->releases()
                ->latest(
                    $repository->getUsername(),
                    $repository->getName(),
                );
        } catch (ExceptionInterface $exception) {
            throw ClientException::createFromPrevious(
                sprintf('Error "%s" - for repository %s', $exception->getMessage(), $repository->getFullName()),
                $exception
            );
        }

        if (!isset($lastRelease['published_at'])) {
            throw new ClientException(
                sprintf(self::MISSING_RELEASE_DATA_ERROR, $repository->getFullName())
            );
        }

        try {
            $lastReleaseDate = new DateTimeImmutable($lastRelease['published_at']);
        } catch (Exception $exception) {
            throw ClientException::createFromPrevious(
                sprintf(self::LAST_RELEASE_DATE_BAD_FORMAT_ERROR, $repository->getFullName()),
                $exception
            );
        }

        return Releases::createWithLastReleaseDate($lastReleaseDate);
    }
}
