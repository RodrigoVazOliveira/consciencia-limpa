<?php

namespace App\Repository;

use App\Entity\Outgoing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Outgoing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outgoing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outgoing[]    findAll()
 * @method Outgoing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutgoingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outgoing::class);
    }

    // /**
    //  * @return Outgoing[] Returns an array of Outgoing objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Outgoing
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
