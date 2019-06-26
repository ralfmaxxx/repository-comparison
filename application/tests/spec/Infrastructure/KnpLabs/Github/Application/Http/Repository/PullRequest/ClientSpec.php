<?php

declare(strict_types=1);

namespace tests\spec\App\Infrastructure\KnpLabs\Github\Application\Http\Repository\PullRequest;

use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\PullRequests;
use App\Infrastructure\KnpLabs\Github\Application\Http\Repository\PullRequest\Client;
use App\Infrastructure\KnpLabs\Github\Application\Http\Response\Counter\PageCounter;
use Github\Api\PullRequest;
use Github\Client as GitHubClient;
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

    private const TEN_OPEN_PRS = 10;
    private const FOUR_CLOSED_PRS = 4;

    function let(GitHubClient $client, PageCounter $pageCounter)
    {
        $this->beConstructedWith($client, $pageCounter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Client::class);
    }

    function it_returns_data_about_repository_pull_requests(
        GithubClient $client,
        PullRequest $pullRequests,
        PageCounter $pageCounter,
        ResponseInterface $firstResponse,
        ResponseInterface $secondResponse
    ) {
        $repository = new Repository(self::USERNAME, self::NAME);

        $expectedPullRequests = new PullRequests(self::TEN_OPEN_PRS, self::FOUR_CLOSED_PRS);

        $client
            ->pullRequests()
            ->shouldBeCalled()
            ->willReturn($pullRequests);

        $pullRequests
            ->all(self::USERNAME, self::NAME, ['per_page' => 1, 'page' => 1, 'state' => 'open'])
            ->shouldBeCalled();

        $pullRequests
            ->all(self::USERNAME, self::NAME, ['per_page' => 1, 'page' => 1, 'state' => 'closed'])
            ->shouldBeCalled();

        $client
            ->getLastResponse()
            ->shouldBeCalledTimes(2)
            ->willReturn(
                $firstResponse,
                $secondResponse
            );

        $pageCounter
            ->count($firstResponse)
            ->shouldBeCalled()
            ->willReturn(self::TEN_OPEN_PRS);

        $pageCounter
            ->count($secondResponse)
            ->shouldBeCalled()
            ->willReturn(self::FOUR_CLOSED_PRS);

        $this
            ->get($repository)
            ->shouldBeLike($expectedPullRequests);
    }

    function it_throws_an_exception_when_could_not_fetch_data(GithubClient $client, PullRequest $pullRequests)
    {
        $repository = new Repository(self::USERNAME, self::NAME);

        $client
            ->pullRequests()
            ->shouldBeCalled()
            ->willReturn($pullRequests);

        $pullRequests
            ->all(self::USERNAME, self::NAME, ['per_page' => 1, 'page' => 1, 'state' => 'open'])
            ->shouldBeCalled()
            ->willThrow(new RuntimeException());

        $pullRequests
            ->all(self::USERNAME, self::NAME, ['per_page' => 1, 'page' => 1, 'state' => 'closed'])
            ->shouldNotBeCalled();

        $this
            ->shouldThrow(ClientException::class)
            ->duringGet($repository);
    }
}