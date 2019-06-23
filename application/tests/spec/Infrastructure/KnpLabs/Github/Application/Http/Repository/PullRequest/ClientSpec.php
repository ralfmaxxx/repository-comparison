<?php

namespace tests\spec\App\Infrastructure\KnpLabs\Github\Application\Http\Repository\PullRequest;

use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\PullRequests;
use App\Infrastructure\KnpLabs\Github\Application\Http\Repository\PullRequest\Client;
use Github\Api\PullRequest;
use Github\Client as GitHubClient;
use Github\Exception\RuntimeException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Client
 */
class ClientSpec extends ObjectBehavior
{
    private const USERNAME = 'username';
    private const NAME = 'name';

    private const ONE_OPEN_PR = 1;
    private const TWO_CLOSED_PR = 2;

    private const ONE_OPEN_AND_TWO_CLOSED_PULL_REQUESTS = [
        [
            'state' => 'open',
        ],
        [
            'state' => 'closed',
        ],
        [
            'state' => 'closed',
        ],
    ];
    private const NO_PULL_REQUESTS = [];

    function let(GitHubClient $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Client::class);
    }

    function it_returns_data_about_repository_pull_requests(GithubClient $client, PullRequest $pullRequests)
    {
        $repository = new Repository(self::USERNAME, self::NAME);

        $expectedPullRequests = new PullRequests(self::ONE_OPEN_PR, self::TWO_CLOSED_PR);

        $client
            ->pullRequests()
            ->shouldBeCalled()
            ->willReturn($pullRequests);

        $pullRequests
            ->all(self::USERNAME, self::NAME, Argument::type('array'))
            ->shouldBeCalledTimes(2)
            ->willReturn(
                self::ONE_OPEN_AND_TWO_CLOSED_PULL_REQUESTS,
                self::NO_PULL_REQUESTS
            );

        $this
            ->get($repository)
            ->shouldBeLike($expectedPullRequests);
    }

    function it_throws_an_exception_when_could_not_fetch_data_using_client(GithubClient $client, PullRequest $pullRequests)
    {
        $repository = new Repository(self::USERNAME, self::NAME);

        $client
            ->pullRequests()
            ->shouldBeCalled()
            ->willReturn($pullRequests);

        $pullRequests
            ->all(self::USERNAME, self::NAME, ['state' => 'all', 'page' => 1])
            ->shouldBeCalled()
            ->willThrow(new RuntimeException());

        $this
            ->shouldThrow(ClientException::class)
            ->duringGet($repository);
    }
}