<?php

namespace App\Repository;

use App\Entity\Incoming;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

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
    
    public function findByDescriptionByIsMothCurrenty($description, \DateTime $date)
    {
        $month = $date->format('m');
        $year  = $date->format('Y');
        $dateIntialMonth = date($year.'-'.$month.'-01');
        $dateFinalMotnh = date('Y-m-t', strtotime($date->format('Y-m-d')));
        
        return $this->createQueryBuilder('i')
            ->where('i.description = :description')
            ->andWhere('i.date between :dateIn AND :dateFinal')
            ->setParameter('description', $description)
            ->setParameter('dateIn', $dateIntialMonth)
            ->setParameter('dateFinal', $dateFinalMotnh)
            ->getQuery()->getOneOrNullResult();
    }
    
    public function findByDescription($description)
    {
        return $this->createQueryBuilder('i')
        ->where('i.description = :description')
        ->setParameter('description', $description)
        ->getQuery()->getResult();
    }
    
    public function findByMoth($month, $year)
    {
        $rsm  = new ResultSetMapping();
        $rsm->addEntityResult(Incoming::class, 'i');
        $rsm->addFieldResult('i', 'id', 'id');
        $rsm->addFieldResult('i', 'description', 'description');
        $rsm->addFieldResult('i', 'value', 'value');
        $rsm->addFieldResult('i', 'date', 'date');

        $query = $this->getEntityManager()
        ->createNativeQuery('SELECT * FROM Incoming WHERE EXTRACT(MONTH FROM date) = :mes AND EXTRACT(YEAR FROM date) = :ano', $rsm);
        $query->setParameter('ano', $year);
        $query->setParameter('mes', $month);
        
        return $query->getResult();
    }
}
