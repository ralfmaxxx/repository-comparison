<?php

namespace tests\spec\App\Infrastructure\KnpLabs\Github\Application\Http\Repository;

use App\Application\Http\Repository\Client as AppilicationClient;
use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\Information;
use App\Application\Http\Repository\Response\PullRequests;
use App\Application\Http\Repository\Response\Releases;
use App\Application\Http\Repository\Response\Statistics;
use App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Client;
use App\Infrastructure\KnpLabs\Github\Application\Http\Repository\PullRequest\Client as PullRequestClient;
use App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Release\Client as ReleaseClient;
use App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Statistics\Client as StatisticsClient;
use DateTimeImmutable;
use Github\Exception\RuntimeException;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Client
 */
class ClientSpec extends ObjectBehavior
{
    private const USERNAME = 'username';
    private const NAME = 'name';

    private const WATCHERS = 4;
    private const STARS = 44;
    private const FORKS = 456;

    private const LAST_RELEASE_DATE = '2018-01-01 12:00:00';
    private const OPEN_COUNT = 1;
    private const CLOSED_COUNT = 22;

    private const EXCEPTION_MESSAGE = 'message';

    function let(
        StatisticsClient $statisticsClient,
        ReleaseClient $releaseClient,
        PullRequestClient $pullRequestClient
    ) {
        $this->beConstructedWith($statisticsClient, $releaseClient, $pullRequestClient);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Client::class);
    }

    function it_implements_application_http_repository_interface()
    {
        $this->shouldBeAnInstanceOf(AppilicationClient::class);
    }

    function it_returns_data_about_repository(
        StatisticsClient $statisticsClient,
        ReleaseClient $releaseClient,
        PullRequestClient $pullRequestClient
    ) {
        $repository = new Repository(self::USERNAME, self::NAME);

        $statistics = new Statistics(self::FORKS, self::STARS, self::WATCHERS);
        $releases = new Releases(new DateTimeImmutable(self::LAST_RELEASE_DATE));
        $pullRequests = new PullRequests(self::OPEN_COUNT, self::CLOSED_COUNT);

        $expectedInformation = new Information($statistics, $releases, $pullRequests);

        $statisticsClient
            ->get($repository)
            ->shouldBeCalled()
            ->willReturn($statistics);

        $releaseClient
            ->get($repository)
            ->shouldBeCalled()
            ->willReturn($releases);

        $pullRequestClient
            ->get($repository)
            ->shouldBeCalled()
            ->willReturn($pullRequests);

        $this
            ->getInformation($repository)
            ->shouldBeLike($expectedInformation);
    }

    function it_throws_an_exception_when_can_not_fetch_data_about_repository(
        StatisticsClient $statisticsClient,
        ReleaseClient $releaseClient,
        PullRequestClient $pullRequestClient
    ) {
        $repository = new Repository(self::USERNAME, self::NAME);

        $exception = ClientException::createFromPrevious(self::EXCEPTION_MESSAGE, new RuntimeException(self::EXCEPTION_MESSAGE));

        $statisticsClient
            ->get($repository)
            ->shouldBeCalled()
            ->willThrow($exception);

        $releaseClient
            ->get($repository)
            ->shouldNotBeCalled();

        $pullRequestClient
            ->get($repository)
            ->shouldNotBeCalled();

        $this
            ->shouldThrow(ClientException::createFromPrevious(self::EXCEPTION_MESSAGE, $exception))
            ->duringGetInformation($repository);
    }
}