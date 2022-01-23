<?php
namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use App\Entity\Outgoing;

final class OutgoingService
{
    private $entityManager;
    private $outgoindRepository;
    private $logger;
    
    public function __construct(ManagerRegistry $managerRegistry, LoggerInterface $looger)
    { 
        $this->entityManager = $managerRegistry->getManager();
        $this->logger = $looger;
        $this->outgoindRepository = $this->entityManager->getRepository(Outgoing::class);
    }
    
    public function save(Outgoing $outgoing): ?Outgoing
    {
        $this->logger->info('save - persistindo a despesa - despesa: '.$outgoing->__toString());
        $this->verifyOutgoingDuplication($outgoing->getDescription(), $outgoing->getDate());
        $this->entityManager->persist($outgoing);
        $this->entityManager->flush();
        
        return $outgoing;
    }
    
    private function verifyOutgoingDuplication($description, $date)
    {
        $this->logger->info('verifyOutgoingDuplication - description: '.$description. ' mês: '.$date->format('m'));
        if ($this->existsEqualsDecriptionsInMonth($description, $date)) {
            $this->logger->info('verifyOutgoingDuplication - existe uma despesa com a descricao');
            throw new \RuntimeException('Já existe uma despesa com descrição informada no mesmo mês.');
        }
    }
    
    private function existsEqualsDecriptionsInMonth($description, $date): bool
    {
        $this->logger->info('existsEqualsDecriptionsInMonth - description: ' . $description);
        if ($this->outgoindRepository->findByDescriptionByIsMothCurrenty($description, $date) != null) {
            $this->logger->error('existsEqualsDecriptionsInMonth - ja existe despesa com a descriçao esse mês - description: ' . $description);
            return true;
        }
        
        return false;
    }
}