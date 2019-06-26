<?php

declare(strict_types=1);

namespace tests\spec\App\Infrastructure\KnpLabs\Github\Application\Http\Response\Counter;

use PhpSpec\ObjectBehavior;
use App\Infrastructure\KnpLabs\Github\Application\Http\Response\Counter\PageCounter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @mixin PageCounter
 */
class PageCounterSpec extends ObjectBehavior
{
    private const LINK_HEADER_CONTAINING_INFORMATION_THAT_THERE_IS_TEN_PAGES = '
        <https://api.github.com/repositories/something/pulls?state=open&per_page=1&page=2>; rel="next",
        <https://api.github.com/repositories/something/pulls?state=open&per_page=1&page=10>; rel="last"
    ';

    private const NO_PAGES = 0;
    private const ONE_PAGE = 1;
    private const TEN_PAGES = 10;

    private const RESPONSE_CONTAING_ONE_RESOURCE = '[{"resourceId" : "xxx"}]';

    function it_is_initializable()
    {
        $this->shouldHaveType(PageCounter::class);
    }

    function it_returns_zero_when_response_does_not_have_any_resources(
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $response
            ->getBody()
            ->shouldBeCalled()
            ->willReturn($stream);

        $response
            ->getHeaderLine('Content-Type')
            ->shouldBeCalled()
            ->willReturn('application/json');

        $stream
            ->__toString()
            ->shouldBeCalled()
            ->willReturn('[]');

        $this
            ->count($response)
            ->shouldReturn(self::NO_PAGES);
    }

    function it_returns_one_when_response_has_one_resource_and_no_link_header(
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $response
            ->getBody()
            ->shouldBeCalled()
            ->willReturn($stream);

        $response
            ->getHeaderLine('Content-Type')
            ->shouldBeCalled()
            ->willReturn('application/json');

        $response
            ->hasHeader('Link')
            ->shouldBeCalled()
            ->willReturn(false);

        $stream
            ->__toString()
            ->shouldBeCalled()
            ->willReturn(self::RESPONSE_CONTAING_ONE_RESOURCE);

        $this
            ->count($response)
            ->shouldReturn(self::ONE_PAGE);
    }

    function it_returns_the_number_of_pages_based_on_link_header(
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $response
            ->getBody()
            ->shouldBeCalled()
            ->willReturn($stream);

        $response
            ->getHeaderLine('Content-Type')
            ->shouldBeCalled()
            ->willReturn('application/json');

        $response
            ->hasHeader('Link')
            ->shouldBeCalled()
            ->willReturn(true);

        $response
            ->getHeader('Link')
            ->shouldBeCalled()
            ->willReturn([self::LINK_HEADER_CONTAINING_INFORMATION_THAT_THERE_IS_TEN_PAGES]);

        $stream
            ->__toString()
            ->shouldBeCalled()
            ->willReturn(self::RESPONSE_CONTAING_ONE_RESOURCE);

        $this
            ->count($response)
            ->shouldReturn(self::TEN_PAGES);
    }

}