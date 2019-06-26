<?php

declare(strict_types=1);

namespace tests\spec\App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Statistics;

use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\Statistics;
use App\Infrastructure\KnpLabs\Github\Application\Http\Repository\Statistics\Client;
use Github\Api\Repo;
use Github\Client as GithubClient;
use Github\Exception\RuntimeException;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Client
 */
class ClientSpec extends ObjectBehavior
{
    private const USERNAME = 'username';
    private const NAME = 'name';

    private const FORKS = 12;
    private const STARTS = 11;
    private const WATCHERS = 44;

    function let(GithubClient $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Client::class);
    }

    function it_returns_basic_statistics_about_repository(GithubClient $client, Repo $repos)
    {
        $repository = new Repository(self::USERNAME, self::NAME);

        $expectedStatistics = new Statistics(self::FORKS, self::STARTS, self::WATCHERS);

        $client
            ->repos()
            ->shouldBeCalled()
            ->willReturn($repos);

        $repos
            ->show(self::USERNAME, self::NAME)
            ->shouldBeCalled()
            ->willReturn([
                'forks_count' => self::FORKS,
                'stargazers_count' => self::STARTS,
                'subscribers_count' => self::WATCHERS,
            ]);

        $this
            ->get($repository)
            ->shouldBeLike($expectedStatistics);
    }

    function it_throws_an_exception_when_there_is_no_needed_data(GithubClient $client, Repo $repos)
    {
        $repository = new Repository(self::USERNAME, self::NAME);

        $client
            ->repos()
            ->shouldBeCalled()
            ->willReturn($repos);

        $repos
            ->show(self::USERNAME, self::NAME)
            ->shouldBeCalled()
            ->willReturn([
                'not_important_data' => 44,
            ]);

        $this
            ->shouldThrow(ClientException::class)
            ->duringGet($repository);
    }

    function it_throws_an_exception_when_could_not_fetch_data(GithubClient $client, Repo $repos)
    {
        $repository = new Repository(self::USERNAME, self::NAME);

        $client
            ->repos()
            ->shouldBeCalled()
            ->willReturn($repos);

        $repos
            ->show(self::USERNAME, self::NAME)
            ->shouldBeCalled()
            ->willThrow(new RuntimeException());

        $this
            ->shouldThrow(ClientException::class)
            ->duringGet($repository);
    }
}