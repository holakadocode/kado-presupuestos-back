<?php

namespace App\Tests\Unit;

use App\Entity\Budget;
use App\Entity\BudgetArticle;
use App\Entity\Client;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

/**
 * Class BudgetArticleTest
 * @package App\Tests\Unit
 * 
 * Test de BudgetArticle class
 */
class BudgetArticleTest extends TestCase
{

    /**
     * Create a single budgetArticle
     * @package App\Tests\Unit
     */
    public function testCreateBudgetArticle()
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

        $articleCode ="01";
        $nameArticle ="Interruptor";
        $quantity =1;
        $price = 15;
        $total = 15;
        $budgetArticle = new BudgetArticle();
        $budgetArticle
            ->setBudget($budget)
            ->setArticleCode($articleCode)
            ->setNameArticle($nameArticle)
            ->setQuantity($quantity)
            ->setPrice($price)
            ->setTotal($total)
        ;    

        //Asertions
        $this->assertEquals($budget, $budgetArticle->getBudget());
        $this->assertEquals($nameArticle, $budgetArticle->getNameArticle());
        $this->assertEquals($articleCode,$budgetArticle->getArticleCode());
        
    }
}
