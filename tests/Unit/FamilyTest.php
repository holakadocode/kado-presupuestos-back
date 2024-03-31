<?php

namespace App\Tests\Unit;

use App\Entity\FamilyFolder;
use PHPUnit\Framework\TestCase;

/**
 * Class FamilyTest
 * @package App\Tests\Unit
 * 
 * Test de Family class
 */
class FamilyTest extends TestCase
{

    /**
     * Create a single Family
     */
    public function testCreateFamily()
    {
        $name = "Interruptores";
        

        $family = new FamilyFolder();
        $family
            ->setName($name)
        ;   

        //Asertions
        $this->assertEquals($name, $family->getName());
       
    }
}
