<?php

declare(strict_types=1);

namespace App\UserInterface\Symfony\Normalizer\Api;

use App\Application\Query\Model\Comparison;
use App\Application\Query\Model\RepositoryStatistics;

final class ComparisonQueryNormalizer
{
    public function normalize(Comparison $comparison): array
    {
        return [
            'id' => $comparison->getId(),
            'firstRepository' => $this->normalizeStatistics($comparison->getFirstRepositoryStatistics()),
            'secondRepository' => $this->normalizeStatistics($comparison->getSecondRepositoryStatistics()),
        ];
    }

    private function normalizeStatistics(RepositoryStatistics $statistics): array
    {
        $basicData = $statistics->getBasicData();

        return [
            'id' => $basicData->getId(),
            'name' => $basicData->getFullName(),
            'status' => $basicData->getStatus(),
            'starsCount' => $statistics->getStars(),
            'forksCount' => $statistics->getForks(),
            'watchersCount' => $statistics->getWatchers(),
            'lastReleaseDate' => $statistics->hasLastReleaseDate() ?
                $statistics->getLastReleaseDate()->format('Y-m-d H:i:s')
                : null,
            'openPRCount' => $statistics->getOpenPrs(),
            'closedPRCount' => $statistics->getClosedPrs(),
        ];
    }
}
