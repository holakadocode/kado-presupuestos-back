<?php

namespace App\Tests\Integration;

use App\Entity\Client;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Class ClientTest
 * @package App\Tests\Integration
 * 
 * Test Client entity
 */
class ClientTest extends KernelTestCase
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
        $client = $this->entityManager
            ->getRepository(Client::class)
            ->findOneBy(['name' => 'Antonio']);

        $this->assertSame('49358479', $client->getNif());
    }
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
