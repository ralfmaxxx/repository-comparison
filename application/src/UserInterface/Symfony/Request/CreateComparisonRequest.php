<?php

declare(strict_types=1);

namespace App\UserInterface\Symfony\Request;

use Symfony\Component\HttpFoundation\Request;

final class CreateComparisonRequest
{
    private const GITHUB_HOST = 'https://github.com';

    private $firstRepositoryName;
    private $secondRepositoryName;

    public function __construct(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $firstRepositoryName = (string) ($data['firstRepository'] ?? '');
        $secondRepositoryName = (string) ($data['secondRepository'] ?? '');

        $this->firstRepositoryName = mb_strpos($firstRepositoryName, self::GITHUB_HOST) === false ?
            sprintf('%s/%s', self::GITHUB_HOST, $firstRepositoryName)
            : $firstRepositoryName;

        $this->secondRepositoryName = mb_strpos($secondRepositoryName, self::GITHUB_HOST) === false ?
            sprintf('%s/%s', self::GITHUB_HOST, $secondRepositoryName)
            : $secondRepositoryName;
    }

    public function getFirstRepositoryName(): string
    {
        return $this->firstRepositoryName;
    }

    public function getSecondRepositoryName(): string
    {
        return $this->secondRepositoryName;
    }
}
