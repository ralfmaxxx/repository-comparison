<?php

declare(strict_types=1);

namespace tests\features\bootstrap;

use Behat\Behat\Context\Context;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Exception;
use Mockery;
use Symfony\Component\HttpKernel\KernelInterface;

final class HooksContext implements Context
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     *
     * @throws Exception
     */
    public function clearDatabase(): void
    {
        $entityManager = $this->kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($entityManager);
        $executor = new ORMExecutor($entityManager, $purger);

        $executor->purge();
        $entityManager->clear();
    }

    /**
     * @AfterScenario
     */
    public function closeMockery(): void
    {
        Mockery::close();
    }
}