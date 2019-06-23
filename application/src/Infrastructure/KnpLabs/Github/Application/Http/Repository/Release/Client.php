<?php

declare(strict_types=1);

namespace App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Release;

use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\Releases;
use DateTimeImmutable;
use Exception;
use Github\Client as GithubClient;
use Github\Exception\ExceptionInterface;

class Client
{
    private const MISSING_RELEASE_DATA_ERROR = 'Missing data about releases for repository "%s".';
    private const LAST_RELEASE_DATE_BAD_FORMAT_ERROR = 'Last release date is in bad format for repository "%s".';

    private $client;

    public function __construct(GithubClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throws ClientException
     */
    public function get(Repository $repository): Releases
    {
        try {
            $lastRelease = $this
                ->client
                ->repo()
                ->releases()
                ->latest($repository->getUsername(), $repository->getName());
        } catch (ExceptionInterface $exception) {
            throw ClientException::createFromPrevious($exception->getMessage(), $exception);
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

        return new Releases($lastReleaseDate);
    }
}
