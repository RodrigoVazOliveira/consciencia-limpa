<?php

namespace App\Repository;

use App\Entity\Incoming;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Incoming|null find($id, $lockMode = null, $lockVersion = null)
 * @method Incoming|null findOneBy(array $criteria, array $orderBy = null)
 * @method Incoming[]    findAll()
 * @method Incoming[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncomingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Incoming::class);
    }

    // /**
    //  * @return Incoming[] Returns an array of Incoming objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Incoming
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
