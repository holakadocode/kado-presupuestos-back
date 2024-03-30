<?php

namespace App\Tests\Unit;

use App\Entity\Client;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientTest
 * @package App\Tests\Unit
 * 
 * Test de Client class
 */
class ClientTest extends TestCase
{

    /**
     * Create a single client
     */
    public function testCreateClient()
    {
        $name= "Antonio";
        $surname = "Rodriguez";
        $taxIdentification = "45454545X";
        $tlf = "956696969";
        $contactEmail = "antonioro@gmail.es";
        $address = "calle 1";
        $cp = "11002";
        $city = "Kentuki";
        

        $client = new Client();
        $client
            ->setName($name)
            ->setSurname($surname)
            ->setTaxIdentification($taxIdentification)
            ->setTlf($tlf)
            ->setContactEmail($contactEmail)
            ->setAddress($address)
            ->setCp($cp)
            ->setCity($city)
            ;

        //Asertions
        $this->assertEquals($surname, $client->getSurname());
        $this->assertEquals($address, $client->getAddress());    
    }
}




