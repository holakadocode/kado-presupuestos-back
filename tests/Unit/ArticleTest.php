<?php

namespace App\Tests\Unit;

use App\Entity\Article;
use App\Entity\FamilyFolder;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

/**
 * Class ArticleTest
 * @package App\Tests\Unit
 * 
 * Test de Article class
 */
class ArticleTest extends TestCase
{

    /**
     * Create a single article
     */
    public function testCreateArticle()
    {
        $family = new FamilyFolder();
        $family
            ->setName("Interruptores");   

        $name = "Interrupor Magnetotermico";
        $description = "Interruptor 16 A 2p";
        $code = "1728";
        $distribuitorCode = "45868";
        $distribuitorPrice = 15;
        $price = 18;

        $article = new ArtiCle();
        $article
            ->setFamilyFolder($family)
            ->setName($name)
            ->setDescription($description)
            ->setCode($code)
            ->setDistributorCode($distribuitorCode)
            ->setDistributorPrice($distribuitorPrice)
            ->setprice($price)
            ;

        //Asertions
        $this->assertEquals($family, $article->getFamilyFolder());
        $this->assertGreaterThan(10,$article->getPrice());
        $this->assertEquals($code,$article->getCode());
      
    }
}
