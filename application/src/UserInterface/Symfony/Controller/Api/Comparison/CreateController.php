<?php

declare(strict_types=1);

namespace App\UserInterface\Symfony\Controller\Api\Comparison;

use App\Application\Command\Comparison\CreateComparison;
use App\Application\Command\Comparison\DeliverStatisticsForComparison;
use App\Application\Command\HandlerException;
use App\Infrastructure\Doctrine\Dbal\Application\Query\ComparisonWithStatisticsQuery;
use App\UserInterface\Symfony\Normalizer\Api\ComparisonQueryNormalizer;
use App\UserInterface\Symfony\Request\CreateComparisonRequest;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Info(
 *     title="API V1",
 *     version="1.0.0"
 * )
 * @OA\Server(
 *     url="http://localhost/api/v1",
 *     description="Local server"
 * )
 * @OA\Schema(
 *      schema="ComparisonRequest",
 *      type="object",
 *      @OA\Property(
 *          property="firstRepository",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="secondRepository",
 *          type="string"
 *      ),
 *      example={
 *          "firstRepository": "https://github.com/ramsey/php-library-skeleton",
 *          "secondRepository": "https://github.com/ramsey/http-range"
 *      }
 * )
 * @OA\Schema(
 *      schema="RepositoryStatisticsResponse",
 *      type="object",
 *      @OA\Property(
 *          property="id",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="starsCount",
 *          type="integer",
 *          nullable=true
 *      ),
 *      @OA\Property(
 *          property="forksCount",
 *          type="integer",
 *          nullable=true
 *      ),
 *      @OA\Property(
 *          property="watchersCount",
 *          type="integer",
 *          nullable=true
 *      ),
 *      @OA\Property(
 *          property="lastReleaseDate",
 *          type="string",
 *          nullable=true
 *      ),
 *      @OA\Property(
 *          property="openPRCount",
 *          type="integer",
 *          nullable=true
 *      ),
 *      @OA\Property(
 *          property="closedPRCount",
 *          type="string",
 *          nullable=true
 *      )
 * )
 * @OA\Schema(
 *      schema="ComparisonResponse",
 *      type="object",
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(property="id", type="string", description="Comparison id."),
 *              @OA\Property(
 *                  property="firstRepository",
 *                  type="object",
 *                  ref="#/components/schemas/RepositoryStatisticsResponse",
 *                  description="First repository statistics."
 *              ),
 *              @OA\Property(
 *                  property="secondRepository",
 *                  type="object",
 *                  ref="#/components/schemas/RepositoryStatisticsResponse",
 *                  description="Second repository statistics."
 *              )
 *          )
 *      }
 * )
 */
final class CreateController
{
    private $commandBus;
    private $query;
    private $normalizer;

    public function __construct(
        CommandBus $commandBus,
        ComparisonWithStatisticsQuery $query,
        ComparisonQueryNormalizer $normalizer
    ) {
        $this->commandBus = $commandBus;
        $this->query = $query;
        $this->normalizer = $normalizer;
    }

    /**
     * @OA\Post(
     *      tags={"Comparison"},
     *      path="/comparison",
     *      description="Creates a new comparison.",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  ref="#/components/schemas/ComparisonRequest"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="201",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/ComparisonResponse"
     *          ),
     *          description="Comparison was created."
     *      ),
     *      @OA\Response(
     *          response="500",
     *          description="Internal server problem."
     *      )
     * )
     */
    public function execute(Request $request): Response
    {
        $comparisonId = Uuid::uuid4()->toString();

        $createComparisonRequest = new CreateComparisonRequest($request);

        $createComparisonCommand = new CreateComparison(
            $comparisonId,
            $createComparisonRequest->getFirstRepositoryName(),
            $createComparisonRequest->getSecondRepositoryName()
        );
        $deliverStatisticsCommand = new DeliverStatisticsForComparison($comparisonId);

        try {
            $this->commandBus->handle($createComparisonCommand);
            $this->commandBus->handle($deliverStatisticsCommand);
        } catch (HandlerException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $comparison = $this->query->findById($comparisonId);

        if (is_null($comparison)) {
            return new JsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(
            $this->normalizer->normalize($comparison),
            Response::HTTP_CREATED
        );
    }
}
