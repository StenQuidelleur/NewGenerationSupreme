<?php

namespace App\Repository;

use App\Entity\SizeProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SizeProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method SizeProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method SizeProduct[]    findAll()
 * @method SizeProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SizeProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SizeProduct::class);
    }

    // /**
    //  * @return SizeProduct[] Returns an array of SizeProduct objects
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
    public function findOneBySomeField($value): ?SizeProduct
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
