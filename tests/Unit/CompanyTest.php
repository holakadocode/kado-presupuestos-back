<?php

namespace App\Tests\Unit;

use App\Entity\Company;
use PHPUnit\Framework\TestCase;

/**
 * Class CompanyTest
 * @package App\Tests\Unit
 * 
 * Test de Company class
 */
class CompanyTest extends TestCase
{

    /**
     * Create a Company data
     */
    public function testCreateCompany()
    {
        $name = "Empresa01";
        $taxIdentification = "B-00000000";
        $address = "address 1";
        $cp = "11002";
        $city = "Kentuki";
        $tlf = "000000000";
        $email = "empresa01@empresa01.es";


        $company = new Company();
        $company
            ->setName($name)
            ->setTaxIdentification($taxIdentification)
            ->setAddress($address)
            ->setCp($cp)
            ->setCity($city)
            ->setPhone($tlf)
            ->setEmail($email)
        ;    

        //Asertions
        $this->assertEquals($name, $company->getname());
        $this->assertEquals($address, $company->getAddress());
    }
}
