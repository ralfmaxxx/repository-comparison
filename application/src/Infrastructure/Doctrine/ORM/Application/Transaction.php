<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM\Application;

use App\Application\Transaction as ApplicationTransaction;
use Doctrine\ORM\EntityManagerInterface;

final class Transaction implements ApplicationTransaction
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function begin(): void
    {
        $this->entityManager->beginTransaction();
    }

    public function commit(): void
    {
        $this->entityManager->commit();

        $this->entityManager->flush();
    }

    public function rollback(): void
    {
        $this->entityManager->rollback();

        $this->entityManager->flush();
    }
}
