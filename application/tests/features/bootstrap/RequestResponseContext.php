<?php

declare(strict_types=1);

namespace tests\features\bootstrap;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit\Framework\Assert;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

final class RequestResponseContext implements Context
{
    private $kernel;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @When I send :type request to :path
     */
    public function iSendRequestTo(string $type, string $path): void
    {
        $this->response = $this->kernel->handle(Request::create($path, $type));
    }

    /**
     * @Then the response status should be :status
     */
    public function theResponseStatusShouldBe(int $status): void
    {
        if ($this->response === null) {
            throw new RuntimeException('No response received');
        }

        Assert::assertEquals($status, $this->response->getStatusCode());
    }

    /**
     * @Then the response should be:
     */
    public function theResponseShouldBe(PyStringNode $string): void
    {
        if ($this->response === null) {
            throw new RuntimeException('No response received');
        }

        Assert::assertEquals(
            json_decode($string->getRaw(), true),
            json_decode($this->response->getContent(), true)
        );
    }
}
