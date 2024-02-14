<?php

namespace App\Repository;

use App\Entity\FamilyFolder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FamilyFolder>
 *
 * @method FamilyFolder|null find($id, $lockMode = null, $lockVersion = null)
 * @method FamilyFolder|null findOneBy(array $criteria, array $orderBy = null)
 * @method FamilyFolder[]    findAll()
 * @method FamilyFolder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamilyFolderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FamilyFolder::class);
    }

//    /**
//     * @return FamilyFolder[] Returns an array of FamilyFolder objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FamilyFolder
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
