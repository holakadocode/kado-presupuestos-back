<?php

namespace App\Tests\Integration;

use App\Entity\Provider;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Class ProviderTest
 * @package App\Tests\Integration
 * 
 * Test Provider entity
 */
class ProviderTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Run before the tests
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSearchByName()
    {
        $provider = $this->entityManager
            ->getRepository(Provider::class)
            ->findOneBy(['nameCompany' => 'Elecam']);

        $this->assertSame('434534', $provider->getNif());
    }
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
