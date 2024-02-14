<?php

namespace App\Repository;

use App\Entity\BudgetArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BudgetArticle>
 *
 * @method BudgetArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method BudgetArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method BudgetArticle[]    findAll()
 * @method BudgetArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetArticle::class);
    }

//    /**
//     * @return BudgetArticle[] Returns an array of BudgetArticle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BudgetArticle
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
