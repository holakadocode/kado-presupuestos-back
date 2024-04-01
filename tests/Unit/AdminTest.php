<?php

namespace App\Tests\Unit;

use App\Entity\Admin;
use PHPUnit\Framework\TestCase;

/**
 * Class AdminTest
 * @package App\Tests\Unit
 * 
 * Test de Admin class
 */
class AdminTest extends TestCase
{

    /**
     * Create a single admin
     */
    public function testCreateAdmin()
    {
        $name = "Antonio";
        $surname = "Rodriguez";
        $email = "antonioro@gmail.es";
        $roles = [];
        $password = "11002";
        $dateTime = new \DateTime();


        $admin = new Admin();
        $admin
            ->setName($name)
            ->setSurname($surname)
            ->setEmail($email)
            ->setRoles($roles)
            ->setPassword($password)
            ->setDateTime($dateTime);

        //Asertions
        $this->assertEquals($surname, $admin->getSurname());
        $this->assertEquals($password, $admin->getPassword());
    }
}
