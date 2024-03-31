<?php

namespace App\Tests\Unit;

use App\Entity\Provider;
use PHPUnit\Framework\TestCase;

/**
 * Class ProviderTest
 * @package App\Tests\Unit
 * 
 * Test de Provider class
 */
class ProviderTest extends TestCase
{

    /**
     * Create a single provider
     */
    public function testCreateProvider()
    {
        $codProvider= "1234";
        $nameCompany = "Elecam";
        $businessName = "Elecam";
        $nif = "B-11565458";
        $contactPerson = "antonio";
        $email = "antonio@elecam.es";
        $address = "calle melancolia";
        $city = "Cadiz";
        $phone = "956255865";
        

        $provider = new Provider();
        $provider
            ->setCodProvider($codProvider)
            ->setNameCompany($nameCompany)
            ->setbusinessName($businessName)
            ->setNif($nif)
            ->setContactPerson($contactPerson)
            ->setEmail($email)
            ->setAddress($address)
            ->setCity($city)
            ->setPhone($phone)
            ;

        //Asertions
        $this->assertEquals($codProvider, $provider->getCodProvider());
        $this->assertEquals($nameCompany, $provider->getNameCompany());    
    }
}




