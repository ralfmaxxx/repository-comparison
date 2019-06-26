<?php

declare(strict_types=1);

namespace tests\spec\App\Infrastructure\Spatie\Application\Http\Url;

use App\Application\Http\Url\Url;
use App\Application\Http\Url\UrlParser as ApplicationUrlParser;
use PhpSpec\ObjectBehavior;
use App\Infrastructure\Spatie\Application\Http\Url\UrlParser;

/**
 * @mixin UrlParser
 */
class UrlParserSpec extends ObjectBehavior
{
    private const SCHEME = 'https';
    private const HOST = 'www.example.com';
    private const FIRST_SEGMENT = 'username';
    private const SECOND_SEGMENT = 'reponame';
    private const THIRD_SEGMENT = 'something';

    private const URL = self::SCHEME . '://' . self::HOST . '/' .self::FIRST_SEGMENT . '/' . self::SECOND_SEGMENT . '/' . self::THIRD_SEGMENT;
    private const EMPTY_URL = '';

    function it_is_initializable()
    {
        $this->shouldHaveType(UrlParser::class);
    }

    function it_implements_application_url_parser_interface()
    {
        $this->shouldBeAnInstanceOf(ApplicationUrlParser::class);
    }

    function it_returns_data_about_host_and_three_first_segments_of_url()
    {
        $this
            ->parse(self::URL)
            ->shouldBeLike(
                new Url(
                    self::SCHEME,
                    self::HOST,
                    self::FIRST_SEGMENT,
                    self::SECOND_SEGMENT,
                    self::THIRD_SEGMENT
                )
            );
    }

    function it_returns_empty_url_when_there_is_no_data()
    {
        $this
            ->parse(self::EMPTY_URL)
            ->shouldBeLike(new Url('', '', '', '', ''));
    }
}