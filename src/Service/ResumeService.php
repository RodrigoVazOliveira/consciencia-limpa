<?php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use App\DTO\ResumeDTO;
use App\DTO\OutgoingCategoryValueDTO;

class ResumeService
{

    private LoggerInterface $logger;

    private EntityManager $entityManager;

    public function __construct(LoggerInterface $logger, ManagerRegistry $managerRegistry)
    {
        $this->logger = $logger;
        $this->entityManager = $managerRegistry->getManager();
    }

    public function createTableView($month, $year)
    {
        $valueTotalIncoming = $this->getValueTotalIncoming($month, $year);
        $valueTotalOutgoing = $this->getValueTotalOutgoing($month, $year);
        $valueBalance = $valueTotalIncoming - $valueTotalOutgoing;

        $resumeDTO = new ResumeDTO();
        $resumeDTO->setValorTotalReceita($valueTotalIncoming);
        $resumeDTO->setValorTotalDespesa($valueTotalOutgoing);
        $resumeDTO->setSaldoFinal($valueBalance);

        $valuesByCategoryOutgoind = $this->getValuesByGroupCategory($month, $year);
        $resumeDTO->setValorDespesaPorCategoria($this->createobjectsCategoryValues($valuesByCategoryOutgoind));

        return $resumeDTO;
    }
        
    private function getValueTotalOutgoing($month, $year) {
        $sql = 'SELECT ';
        $sql .= 'SUM(value) AS total_value_outgoing ';
        $sql .= 'FROM outgoing ';
        $sql .= 'WHERE EXTRACT(MONTH FROM date) = :mesDespesa AND EXTRACT(YEAR FROM date) = :anoDespesa';
        
        $query = $this->entityManager->getConnection()->prepare($sql);
        $query->bindValue('mesDespesa', $month);
        $query->bindValue('anoDespesa', $year);
        $result = $query->executeQuery();
        $valueOutgoing = $result->fetchAssociative();
        
        if ($valueOutgoing['total_value_outgoing'] == null) {
            return 0;
        }
        
        return $valueOutgoing['total_value_outgoing'];
    }

    private function getValueTotalIncoming($month, $year)
    {
        $sql = 'SELECT ';
        $sql .= 'SUM(value) as total_value_incoming ';
        $sql .= 'FROM incoming ';
        $sql .= 'WHERE EXTRACT(MONTH FROM date) = :mesReceita AND EXTRACT(YEAR FROM date) = :anoReceita';

        $query = $this->entityManager->getConnection()->prepare($sql);
        $query->bindValue('mesReceita', $month);
        $query->bindValue('anoReceita', $year);
        $result = $query->executeQuery();
        $valueIncoming = $result->fetchAssociative();
        
        if ($valueIncoming['total_value_incoming'] == null) {
            return 0;
        }
        
        return $valueIncoming['total_value_incoming'];
    }

    private function getValuesByGroupCategory($month, $year)
    {
        $sql = 'SELECT c.name AS name_category, SUM(o.value) AS total_value ';
        $sql .= 'FROM outgoing o, category c ';
        $sql .= ' WHERE o.category_id = c.id AND ';
        $sql .= ' EXTRACT(MONTH FROM o.date) = :mes AND EXTRACT(YEAR FROM o.date) = :ano';
        $sql .= ' GROUP BY c.name ORDER BY c.name;';

        $query = $this->entityManager->getConnection()->prepare($sql);
        $query->bindValue('ano', $year);
        $query->bindValue('mes', $month);
        $result = $query->executeQuery();
        $values = $result->fetchAllAssociative();

        return $values;
    }

    private function createobjectsCategoryValues($resumeCategory)
    {
        $categoryValues = array();
        foreach ($resumeCategory as $category) {
            $categoryValue = new OutgoingCategoryValueDTO();
            $categoryValue->setNomeCategoria($category['name_category']);
            $categoryValue->setValorTotalDespesa($category['total_value']);

            array_push($categoryValues, $categoryValue);
        }

        return $categoryValues;
    }
}

