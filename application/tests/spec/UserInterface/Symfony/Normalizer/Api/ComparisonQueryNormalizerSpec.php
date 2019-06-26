<?php

declare(strict_types=1);

namespace tests\spec\App\UserInterface\Symfony\Normalizer\Api;

use App\Application\Query\Model\BasicData;
use App\Application\Query\Model\RepositoryStatistics;
use DateTimeImmutable;
use PhpSpec\ObjectBehavior;
use App\UserInterface\Symfony\Normalizer\Api\ComparisonQueryNormalizer;
use App\Application\Query\Model\Comparison;

/**
 * @mixin ComparisonQueryNormalizer
 */
class ComparisonQueryNormalizerSpec extends ObjectBehavior
{
    private const ID = 'xxxx-yyyy-zzzz-aaaa';

    private const FIRST_REPO_ID = 'ee22ea83-4b1c-41bc-bc97-ac4b60750291';
    private const FIRST_REPO_USERNAME = 'first-username';
    private const FIRST_REPO_NAME = 'first-name';
    private const FIRST_REPO_STATUS = 'delivered';
    private const FIRST_REPO_FORKS = 33;
    private const FIRST_REPO_STARS = 587;
    private const FIRST_REPO_WATCHERS = 3;
    private const FIRST_REPO_LAST_RELEASE_DATE = '2018-03-04 12:34:40';
    private const FIRST_REPO_OPEN_PRS = 335;
    private const FIRST_REPO_CLOSED_PRS = 555;

    private const SECOND_REPO_ID = 'ee22ea83-4b1c-41bc-bc97-ac4b60750291';
    private const SECOND_REPO_USERNAME = 'second-username';
    private const SECOND_REPO_NAME = 'second-name';
    private const SECOND_REPO_STATUS = 'delivered';
    private const SECOND_REPO_FORKS = 11;
    private const SECOND_REPO_STARS = 441;
    private const SECOND_REPO_WATCHERS = 567;
    private const SECOND_REPO_LAST_RELEASE_DATE = '2013-03-02 11:00:00';
    private const SECOND_REPO_OPEN_PRS = 0;
    private const SECOND_REPO_CLOSED_PRS = 0;

    private const FIRST_REPOSITORY = [
        'id' => self::FIRST_REPO_ID,
        'name' => self::FIRST_REPO_USERNAME . '/' . self::FIRST_REPO_NAME,
        'status' => self::FIRST_REPO_STATUS,
        'starsCount' => self::FIRST_REPO_STARS,
        'forksCount' => self::FIRST_REPO_FORKS,
        'watchersCount' => self::FIRST_REPO_WATCHERS,
        'lastReleaseDate' => self::FIRST_REPO_LAST_RELEASE_DATE,
        'openPRCount' => self::FIRST_REPO_OPEN_PRS,
        'closedPRCount' => self::FIRST_REPO_CLOSED_PRS,
    ];

    private const SECOND_REPOSITORY = [
        'id' => self::SECOND_REPO_ID,
        'name' => self::SECOND_REPO_USERNAME . '/' . self::SECOND_REPO_NAME,
        'status' => self::SECOND_REPO_STATUS,
        'starsCount' => self::SECOND_REPO_STARS,
        'forksCount' => self::SECOND_REPO_FORKS,
        'watchersCount' => self::SECOND_REPO_WATCHERS,
        'lastReleaseDate' => self::SECOND_REPO_LAST_RELEASE_DATE,
        'openPRCount' => self::SECOND_REPO_OPEN_PRS,
        'closedPRCount' => self::SECOND_REPO_CLOSED_PRS,
    ];

    function it_is_initializable()
    {
        $this->shouldHaveType(ComparisonQueryNormalizer::class);
    }

    function it_normalizes_query_response(Comparison $comparison)
    {
        $comparison
            ->getId()
            ->willReturn(self::ID);

        $firstRepositoryStatistics = new RepositoryStatistics(
            new BasicData(
                self::FIRST_REPO_ID,
                self::FIRST_REPO_USERNAME,
                self::FIRST_REPO_NAME,
                self::FIRST_REPO_STATUS
            ),
            self::FIRST_REPO_FORKS,
            self::FIRST_REPO_STARS,
            self::FIRST_REPO_WATCHERS,
            new DateTimeImmutable(self::FIRST_REPO_LAST_RELEASE_DATE),
            self::FIRST_REPO_OPEN_PRS,
            self::FIRST_REPO_CLOSED_PRS
        );

        $comparison
            ->getFirstRepositoryStatistics()
            ->willReturn($firstRepositoryStatistics);

        $secondRepositoryStatistics = new RepositoryStatistics(
            new BasicData(
                self::SECOND_REPO_ID,
                self::SECOND_REPO_USERNAME,
                self::SECOND_REPO_NAME,
                self::SECOND_REPO_STATUS
            ),
            self::SECOND_REPO_FORKS,
            self::SECOND_REPO_STARS,
            self::SECOND_REPO_WATCHERS,
            new DateTimeImmutable(self::SECOND_REPO_LAST_RELEASE_DATE),
            self::SECOND_REPO_OPEN_PRS,
            self::SECOND_REPO_CLOSED_PRS
        );

        $comparison
            ->getSecondRepositoryStatistics()
            ->willReturn($secondRepositoryStatistics);

        $this
            ->normalize($comparison)
            ->shouldReturn(
                [
                    'id' => self::ID,
                    'firstRepository' => self::FIRST_REPOSITORY,
                    'secondRepository' => self::SECOND_REPOSITORY,
                ]
            );
    }
}