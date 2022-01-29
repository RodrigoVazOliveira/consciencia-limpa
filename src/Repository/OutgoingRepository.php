<?php

namespace App\Repository;

use App\Entity\Outgoing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

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
    
    public function findByDescriptionByIsMothCurrenty($description, \DateTime $date)
    {
        $month = $date->format('m');
        $year  = $date->format('Y');
        $dateIntialMonth = date($year.'-'.$month.'-01');
        $dateFinalMotnh = date('Y-m-t', strtotime($date->format('Y-m-d')));
        
        return $this->createQueryBuilder('o')
        ->where('o.description = :description')
        ->andWhere('o.date between :dateIn AND :dateFinal')
        ->setParameter('description', $description)
        ->setParameter('dateIn', $dateIntialMonth)
        ->setParameter('dateFinal', $dateFinalMotnh)
        ->getQuery()->getOneOrNullResult();
    }
    
    public function findByDescription($description)
    {
        return $this->createQueryBuilder('o')
        ->where('o.description = :description')
        ->setParameter('description', $description)
        ->getQuery()->getResult();
    }
}