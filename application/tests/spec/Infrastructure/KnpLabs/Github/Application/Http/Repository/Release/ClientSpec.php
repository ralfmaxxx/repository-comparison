<?php

declare(strict_types=1);

namespace tests\spec\App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Release;

use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\Releases;
use App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Release\Client;
use App\Infrastructure\KnpLabs\Github\Application\Http\Response\Counter\PageCounter;
use DateTimeImmutable;
use Github\Api\Repo;
use Github\Api\Repository\Releases as GithubReleases;
use Github\Client as GithubClient;
use Github\Exception\RuntimeException;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;

/**
 * @mixin Client
 */
class ClientSpec extends ObjectBehavior
{
    private const USERNAME = 'username';
    private const NAME = 'name';

    private const LAST_RELEASE_DATE = '2018-02-03';
    private const NO_RELEASES = 0;
    private const FOUR_RELEASES = 4;

    private const LAST_RELEASE_DATA = [
        'published_at' => self::LAST_RELEASE_DATE,
    ];

    private const BAD_DATE_FORMAT = 'xyz';

    function let(
        GithubClient $client,
        PageCounter $pageCounter
    ) {
        $this->beConstructedWith($client, $pageCounter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Client::class);
    }

    function it_returns_data_when_repository_does_not_have_any_releases(
        GithubClient $client,
        Repo $repo,
        PageCounter $pageCounter,
        GithubReleases $releases,
        ResponseInterface $allReleasesResponse
    ) {
        $repository = new Repository(self::USERNAME, self::NAME);

        $expectedReleases = Releases::createWithoutLastRelease();

        $client
            ->repo()
            ->shouldBeCalled()
            ->willReturn($repo);

        $repo
            ->releases()
            ->shouldBeCalled()
            ->willReturn($releases);

        $releases
            ->all(self::USERNAME, self::NAME, ['page' => 1, 'per_page' => 1])
            ->shouldBeCalled();

        $client
            ->getLastResponse()
            ->shouldBeCalled()
            ->willReturn($allReleasesResponse);

        $pageCounter
            ->count($allReleasesResponse)
            ->shouldBeCalled()
            ->willReturn(self::NO_RELEASES);

        $this
            ->get($repository)
            ->shouldBeLike($expectedReleases);
    }

    function it_returns_data_when_repository_has_some_releases(
        GithubClient $client,
        Repo $repo,
        PageCounter $pageCounter,
        GithubReleases $releases,
        ResponseInterface $allReleasesResponse
    ) {
        $repository = new Repository(self::USERNAME, self::NAME);

        $expectedReleases = Releases::createWithLastReleaseDate(new DateTimeImmutable(self::LAST_RELEASE_DATE));

        $client
            ->repo()
            ->shouldBeCalled()
            ->willReturn($repo);

        $repo
            ->releases()
            ->shouldBeCalled()
            ->willReturn($releases);

        $releases
            ->all(self::USERNAME, self::NAME, ['page' => 1, 'per_page' => 1])
            ->shouldBeCalled();

        $client
            ->getLastResponse()
            ->shouldBeCalled()
            ->willReturn($allReleasesResponse);

        $pageCounter
            ->count($allReleasesResponse)
            ->shouldBeCalled()
            ->willReturn(self::FOUR_RELEASES);

        $releases
            ->latest(self::USERNAME, self::NAME)
            ->shouldBeCalled()
            ->willReturn(self::LAST_RELEASE_DATA);

        $this
            ->get($repository)
            ->shouldBeLike($expectedReleases);
    }

    function it_throws_an_exception_when_could_not_fetch_data(
        GithubClient $client,
        Repo $repo,
        GithubReleases $releases
    ) {
        $repository = new Repository(self::USERNAME, self::NAME);

        $client
            ->repo()
            ->shouldBeCalled()
            ->willReturn($repo);

        $repo
            ->releases()
            ->shouldBeCalled()
            ->willReturn($releases);

        $releases
            ->all(self::USERNAME, self::NAME, ['page' => 1, 'per_page' => 1])
            ->shouldBeCalled()
            ->willThrow(new RuntimeException());

        $this
            ->shouldThrow(ClientException::class)
            ->duringGet($repository);
    }

    function it_throws_an_exception_when_there_is_no_date_of_the_last_release_in_the_response(
        GithubClient $client,
        Repo $repo,
        PageCounter $pageCounter,
        GithubReleases $releases,
        ResponseInterface $allReleasesResponse
    ) {
        $repository = new Repository(self::USERNAME, self::NAME);

        $client
            ->repo()
            ->shouldBeCalled()
            ->willReturn($repo);

        $repo
            ->releases()
            ->shouldBeCalled()
            ->willReturn($releases);

        $releases
            ->all(self::USERNAME, self::NAME, ['page' => 1, 'per_page' => 1])
            ->shouldBeCalled();

        $client
            ->getLastResponse()
            ->shouldBeCalled()
            ->willReturn($allReleasesResponse);

        $pageCounter
            ->count($allReleasesResponse)
            ->shouldBeCalled()
            ->willReturn(self::FOUR_RELEASES);

        $releases
            ->latest(self::USERNAME, self::NAME)
            ->shouldBeCalled()
            ->willReturn([
                'something_about_release' => 'But missing index containing last release date!',
            ]);

        $this
            ->shouldThrow(ClientException::class)
            ->duringGet($repository);
    }

    function it_throws_an_exception_when_there_is_last_release_date_but_in_bad_format(
        GithubClient $client,
        Repo $repo,
        PageCounter $pageCounter,
        GithubReleases $releases,
        ResponseInterface $allReleasesResponse
    ) {
        $repository = new Repository(self::USERNAME, self::NAME);

        $client
            ->repo()
            ->shouldBeCalled()
            ->willReturn($repo);

        $repo
            ->releases()
            ->shouldBeCalled()
            ->willReturn($releases);

        $releases
            ->all(self::USERNAME, self::NAME, ['page' => 1, 'per_page' => 1])
            ->shouldBeCalled();

        $client
            ->getLastResponse()
            ->shouldBeCalled()
            ->willReturn($allReleasesResponse);

        $pageCounter
            ->count($allReleasesResponse)
            ->shouldBeCalled()
            ->willReturn(self::FOUR_RELEASES);

        $releases
            ->latest(self::USERNAME, self::NAME)
            ->shouldBeCalled()
            ->willReturn([
                'published_at' => self::BAD_DATE_FORMAT,
            ]);

        $this
            ->shouldThrow(ClientException::class)
            ->duringGet($repository);
    }
}