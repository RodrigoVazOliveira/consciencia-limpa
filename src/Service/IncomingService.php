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

    public function save(Incoming $incoming): ?Incoming
    {
        $this->logger->info('save - $incoming: ' . $incoming->__toString());
        if (! $this->existsEqualsDecriptionsInMonth($incoming->getDescription(), $incoming->getDate())) {
            $this->logger->error('save - ja existe receita nesse mes com essa descrico');
            return null;
        }

        $this->entityManager->persist($incoming);
        $this->entityManager->flush();

        return $incoming;
    }

    public function getAll()
    {
        $this->logger->info('getAll - obtendo todas as receitas no banco de dados!');
        return $this->incomingRepository->findAll();
    }

    public function findById($id)
    {
        $this->logger->info('findById - obtendo todas as receitas no banco de dados por id, id: ' . $id);
        $incoming = $this->incomingRepository->find($id);

        if ($incoming == null) :
            $this->logger->error('findById - Não foi encontrado uma receita com id ' . $id);
            throw new \RuntimeException('Não foi encontrado uma receita com id ' . $id);
        endif;

        return $incoming;
    }

    public function update(int $id, Incoming $incoming): ?Incoming
    {
        $this->logger->info('update - atualizando receita - id: ' . $id);
        if (!$this->existsEqualsDecriptionsInMonth($incoming->getDescription(), $incomingOld->getDate())) {
            $this->logger->error('update - Já existe uma receita com essa descrição nesse mês, descricao: '.$incomingOld->getDescription());
            throw new \RuntimeException('Já existe uma receita com essa descrição nesse mês');
        }
        
        $incomingOld = $this->findById($id);
        $incomingOld->setDescription($incoming->getDescription());
        $incomingOld->setValue($incoming->getValue());
        $incomingOld->setDate($incoming->getDate());
        $this->entityManager->flush();

        return $incomingOld;
    }

    public function delete(int $id)
    {
        $incomingDelete = $this->findById($id);
        $this->entityManager->remove($incomingDelete);
        $this->entityManager->flush();
    }
    
    private function existsEqualsDecriptionsInMonth($description, $date): bool
    {
        $this->logger->info('existsEqualsDecriptionsInMonth - description: ' . $description);
        if ($this->incomingRepository->findByDescriptionByIsMothCurrenty($description, $date) != null) {
            $this->logger->error('existsEqualsDecriptionsInMonth - ja existe receita esse mes - description: ' . $description);
            return false;
        }

        return true;
    }
}