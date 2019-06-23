<?php

declare(strict_types=1);

namespace App\UserInterface\Symfony\Controller\Api\Comparison;

use App\Infrastructure\Doctrine\Dbal\Application\Query\ComparisonWithStatisticsQuery;
use App\UserInterface\Symfony\Normalizer\Api\ComparisonQueryNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class GetController
{
    private $query;
    private $normalizer;

    public function __construct(
        ComparisonWithStatisticsQuery $query,
        ComparisonQueryNormalizer $normalizer
    ) {
        $this->query = $query;
        $this->normalizer = $normalizer;
    }

    /**
     * @OA\Get(
     *      tags={"Comparison"},
     *      path="/comparison/{id}",
     *      description="Returns data about comparison.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/ComparisonResponse"
     *          ),
     *          description="Comparison was found."
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="Comparison was not found."
     *      )
     * )
     */
    public function execute(string $id): Response
    {
        $comparison = $this->query->findById($id);

        if (is_null($comparison)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $this->normalizer->normalize($comparison)
        );
    }
}
