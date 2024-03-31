<?php

namespace App\Tests\Unit;

use App\Entity\Budget;
use App\Entity\Client;
use PHPUnit\Framework\TestCase;

/**
 * Class BudgetTest
 * @package App\Tests\Unit
 * 
 * Test de Budget class
 */
class BudgetTest extends TestCase
{

    /**
     * Create a single budget
     */
    public function testCreateBudget()
    {   
        $client = new Client();
        $client
            ->setName("Antonio")
            ->setSurname("Rodriguez")
            ->setTaxIdentification("45454545X")
            ->setTlf("956696969")
            ->setContactEmail("antonioro@gmail.es")
            ->setAddress("calle 1")
            ->setCp("11002")
            ->setCity("Kentuki");

        $dateTime = new \DateTime();
        $title = "Presupuesto1";
        $subTotal = 1000;
        $iva = 21;
        $total = 1210;

        $budget = new Budget();
        $budget
            ->setClient($client)
            ->setDateTime($dateTime)
            ->setTitle($title)
            ->setSubTotal($subTotal)
            ->setIva($iva)
            ->setTotal($total);
       
        //Asertions
        $this->assertEquals($client, $budget->getClient());
        $this->assertEquals($iva,$budget->getIva());
        $this->assertGreaterThan(16,$budget->getIva());
        $this->assertEquals($total, $budget->getTotal());
    }
}
