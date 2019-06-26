<?php

declare(strict_types=1);

namespace App\UserInterface\Symfony\Validator;

use App\Application\Http\Url\UrlParser;
use App\UserInterface\Symfony\Validator\Exception\ValidatorException;

final class GithubRepositoryNameValidator
{
    private const BAD_REPOSITORY_NAME =
        'Bad repository name. Use "https://github.com/username/name" or "username/name"';

    private const GITHUB_HOST = 'github.com';

    private $urlParser;

    public function __construct(UrlParser $urlParser)
    {
        $this->urlParser = $urlParser;
    }

    /**
     * @throws ValidatorException
     */
    public function validate(string $name): void
    {
        $url = $this->urlParser->parse($name);

        if ($url->containsNotEmptyData() && $url->isSecuredHost(self::GITHUB_HOST)) {
            return;
        }

        if ($url->hasOnlyFirstTwoNotEmptySegments() && $url->hasNoHostAndScheme()) {
            return;
        }

        throw new ValidatorException(self::BAD_REPOSITORY_NAME);
    }
}
