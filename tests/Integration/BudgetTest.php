<?php

namespace App\Tests\Integration;

use App\Entity\Budget;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Class BudgetTest
 * @package App\Tests\Integration
 * 
 * Test Budget entity
 */
class BudgetTest extends KernelTestCase
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

    public function testSearchByTitle()
    {
        $budget = $this->entityManager
            ->getRepository(Budget::class)
            ->findOneBy(['budgetID' => 'P-0003']);

        $this->assertSame('Presupuesto 1', $budget->getTitle());
    }
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
