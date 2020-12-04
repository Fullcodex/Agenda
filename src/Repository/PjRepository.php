<?php

namespace App\Repository;

use App\Entity\Pj;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pj|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pj|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pj[]    findAll()
 * @method Pj[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PjRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pj::class);
    }

    // /**
    //  * @return Pj[] Returns an array of Pj objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pj
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
