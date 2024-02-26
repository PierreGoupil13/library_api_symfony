<?php

namespace App\Tests\integrationTests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class IntegrationTestTools extends KernelTestCase
{

    protected EntityManagerInterface $entityManager;
    protected Container $container;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->entityManager = $this->container->get(EntityManagerInterface::class);
        $this->entityManager->beginTransaction();
    }

    public function tearDown(): void
    {
        $this->entityManager->rollback();
    }
}