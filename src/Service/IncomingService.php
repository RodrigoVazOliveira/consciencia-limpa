<?php
namespace App\Service;

use App\Entity\Incoming;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class IncomingService
{
    private $entityManager;
    private $incomingRepository;
    private $logger;
    
    public function __construct(ManagerRegistry $managerRegistry, LoggerInterface $looger) 
    {
        $this->entityManager = $managerRegistry->getManager();
        $this->incomingRepository = $this->entityManager->getRepository(Incoming::class);
        $this->logger = $looger;
    }
    
    public function save(Incoming $incoming):?Incoming
    {
        $this->logger->info('save - $incoming: '. $incoming->__toString());
        if (!$this->existsEqualsDecriptionsInMonth($incoming->getDescription(), $incoming->getDate())) {
            $this->logger->error('save - ja existe receita nesse mes com essa descrico');
            return null;
        }
        
        $this->entityManager->persist($incoming);
        $this->entityManager->flush();
        
        return $incoming;
    }
    
    private function existsEqualsDecriptionsInMonth($description, $date):bool
    {
        $this->logger->info('existsEqualsDecriptionsInMonth - description: '. $description);
        if ($this->incomingRepository->findByDescriptionByIsMothCurrenty($description, $date) != null) {
            $this->logger->error('existsEqualsDecriptionsInMonth - ja existe receita esse mes - description: '. $description);
            return false;
        }
        
        return true;
    }
}