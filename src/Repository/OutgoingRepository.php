<?php
namespace App\Repository;

use App\Entity\Outgoing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use App\Entity\Category;

/**
 *
 * @method Outgoing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outgoing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outgoing[] findAll()
 * @method Outgoing[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
        $year = $date->format('Y');
        $dateIntialMonth = date($year . '-' . $month . '-01');
        $dateFinalMotnh = date('Y-m-t', strtotime($date->format('Y-m-d')));

        return $this->createQueryBuilder('o')
            ->where('o.description = :description')
            ->andWhere('o.date between :dateIn AND :dateFinal')
            ->setParameter('description', $description)
            ->setParameter('dateIn', $dateIntialMonth)
            ->setParameter('dateFinal', $dateFinalMotnh)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByDescription($description)
    {
        return $this->createQueryBuilder('o')
            ->where('o.description = :description')
            ->setParameter('description', $description)
            ->getQuery()
            ->getResult();
    }
    
    public function findByMoth($month, $year)
    {
        $rsm  = new ResultSetMapping();
        $rsm->addEntityResult(Outgoing::class, 'o');
        $rsm->addFieldResult('o', 'id', 'id');
        $rsm->addFieldResult('o', 'description', 'description');
        $rsm->addFieldResult('o', 'value', 'value');
        $rsm->addFieldResult('o', 'date', 'date');
        $rsm->addJoinedEntityResult(Category::class, 'c', 'o', 'category');
        $rsm->addFieldResult('c', 'category_id', 'id');
        $rsm->addFieldResult('c', 'name', 'name');
        
        $sql = 'SELECT o.id, o.description, o.value, o.date, c.id as category_id, c.name  FROM outgoing o ';
        $sql .= 'INNER JOIN category c ON c.id = o.category_id ';
        $sql .= 'WHERE EXTRACT(MONTH FROM date) = :mes AND EXTRACT(YEAR FROM date) = :ano';
        
        $query = $this->getEntityManager()
        ->createNativeQuery($sql, $rsm);
        $query->setParameter('ano', $year);
        $query->setParameter('mes', $month);
        
        return $query->getResult();
    }
}