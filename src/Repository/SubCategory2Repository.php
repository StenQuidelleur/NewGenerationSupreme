<?php

namespace App\Repository;

use App\Entity\SubCategory2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubCategory2|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubCategory2|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubCategory2[]    findAll()
 * @method SubCategory2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubCategory2Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubCategory2::class);
    }

    // /**
    //  * @return SubCategory2[] Returns an array of SubCategory2 objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SubCategory2
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
