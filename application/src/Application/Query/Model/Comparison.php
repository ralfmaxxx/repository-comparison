<?php

declare(strict_types=1);

namespace App\Application\Query\Model;

use DateTimeImmutable;
use Exception;

class Comparison
{
    private $id;
    private $firstRepositoryStatistics;
    private $secondRepositoryStatistics;

    private function __construct(
        string $id,
        RepositoryStatistics $firstRepositoryStatistics,
        RepositoryStatistics $secondRepositoryStatistics
    ) {
        $this->id = $id;
        $this->firstRepositoryStatistics = $firstRepositoryStatistics;
        $this->secondRepositoryStatistics = $secondRepositoryStatistics;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public static function createFrom(array $data): self
    {
        try {
            $firstReleaseDate = !is_null($data['first_repository_statistics_last_release_date'] ?? null)
                ? new DateTimeImmutable($data['first_repository_statistics_last_release_date'])
                : null;
        } catch (Exception $exception) {
            $firstReleaseDate = null;
        }

        $firstRepositoryStatistics = new RepositoryStatistics(
            new BasicData(
                isset($data['first_repository_statistics_id']) ?
                    (string) $data['first_repository_statistics_id']
                    : '',
                isset($data['first_repository_statistics_username']) ?
                    (string) $data['first_repository_statistics_username']
                    : '',
                isset($data['first_repository_statistics_name']) ?
                    (string) $data['first_repository_statistics_name']
                    : '',
                isset($data['first_repository_statistics_status']) ?
                    (string) $data['first_repository_statistics_status']
                    : ''
            ),
            isset($data['first_repository_statistics_forks_count']) ?
                (int) $data['first_repository_statistics_forks_count']
                : null,
            isset($data['first_repository_statistics_stars_count']) ?
                (int) $data['first_repository_statistics_stars_count']
                : null,
            isset($data['first_repository_statistics_watchers_count']) ?
                (int) $data['first_repository_statistics_watchers_count']
                : null,
            $firstReleaseDate,
            isset($data['first_repository_statistics_open_pr_count']) ?
                (int) $data['first_repository_statistics_open_pr_count']
                : null,
            isset($data['first_repository_statistics_closed_pr_count']) ?
                (int) $data['first_repository_statistics_closed_pr_count']
                : null
        );

        try {
            $secondReleaseDate = !is_null($data['second_repository_statistics_last_release_date'] ?? null)
                ? new DateTimeImmutable($data['second_repository_statistics_last_release_date'])
                : null;
        } catch (Exception $exception) {
            $secondReleaseDate = null;
        }

        $secondRepositoryStatistics = new RepositoryStatistics(
            new BasicData(
                isset($data['second_repository_statistics_id']) ?
                    (string) $data['second_repository_statistics_id']
                    : '',
                isset($data['second_repository_statistics_username']) ?
                (string) $data['second_repository_statistics_username']
                : '',
                isset($data['second_repository_statistics_name']) ?
                (string) $data['second_repository_statistics_name']
                : '',
                isset($data['second_repository_statistics_status']) ?
                (string) $data['second_repository_statistics_status']
                : ''
            ),
            isset($data['second_repository_statistics_forks_count']) ?
                (int) $data['second_repository_statistics_forks_count']
                : null,
            isset($data['second_repository_statistics_stars_count']) ?
                (int) $data['second_repository_statistics_stars_count']
                : null,
            isset($data['second_repository_statistics_watchers_count']) ?
                (int) $data['second_repository_statistics_watchers_count']
                : null,
            $secondReleaseDate,
            isset($data['second_repository_statistics_open_pr_count']) ?
                (int) $data['second_repository_statistics_open_pr_count']
                : null,
            isset($data['second_repository_statistics_closed_pr_count']) ?
                (int) $data['second_repository_statistics_closed_pr_count']
                : null
        );

        return new self(
            $data['comparison_id'] ?? '',
            $firstRepositoryStatistics,
            $secondRepositoryStatistics
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstRepositoryStatistics(): RepositoryStatistics
    {
        return $this->firstRepositoryStatistics;
    }

    public function getSecondRepositoryStatistics(): RepositoryStatistics
    {
        return $this->secondRepositoryStatistics;
    }
}
