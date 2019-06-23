<?php

namespace tests\spec\App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Release;

use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\Releases;
use DateTimeImmutable;
use Github\Api\Repo;
use Github\Api\Repository\Releases as GithubReleases;
use Github\Exception\RuntimeException;
use PhpSpec\ObjectBehavior;
use App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Release\Client;
use Github\Client as GithubClient;

/**
 * @mixin Client
 */
class ClientSpec extends ObjectBehavior
{
    private const USERNAME = 'username';
    private const NAME = 'name';

    private const LAST_RELEASE_DATE = '2018-02-03';

    function let(GithubClient $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Client::class);
    }

    function it_returns_date_about_repository_releases(GithubClient $client, Repo $repo, GithubReleases $releases)
    {
        $repository = new Repository(self::USERNAME, self::NAME);

        $expectedReleases = new Releases(new DateTimeImmutable(self::LAST_RELEASE_DATE));

        $client
            ->repo()
            ->shouldBeCalled()
            ->willReturn($repo);

        $repo
            ->releases()
            ->shouldBeCalled()
            ->willReturn($releases);

        $releases
            ->latest(self::USERNAME, self::NAME)
            ->shouldBeCalled()
            ->willReturn([
                'published_at' => self::LAST_RELEASE_DATE,
            ]);

        $this
            ->get($repository)
            ->shouldBeLike($expectedReleases);
    }

    function it_throws_an_exception_when_could_not_fetch_data_using_client(GithubClient $client, Repo $repo, GithubReleases $releases)
    {
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
            ->latest(self::USERNAME, self::NAME)
            ->shouldBeCalled()
            ->willThrow(new RuntimeException());

        $this
            ->shouldThrow(ClientException::class)
            ->duringGet($repository);
    }

    function it_throws_an_exception_when_there_is_no_data_about_it(GithubClient $client, Repo $repo, GithubReleases $releases)
    {
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
            ->latest(self::USERNAME, self::NAME)
            ->shouldBeCalled()
            ->willReturn([
            ]);

        $this
            ->shouldThrow(ClientException::class)
            ->duringGet($repository);
    }

    function it_throws_an_exception_when_there_is_last_release_date_but_in_bad_format(GithubClient $client, Repo $repo, GithubReleases $releases)
    {
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
            ->latest(self::USERNAME, self::NAME)
            ->shouldBeCalled()
            ->willReturn([
                'published_at' => 'xxx',
            ]);

        $this
            ->shouldThrow(ClientException::class)
            ->duringGet($repository);
    }
}