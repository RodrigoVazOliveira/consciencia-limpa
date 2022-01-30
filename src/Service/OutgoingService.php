<?php
namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use App\Entity\Outgoing;
use Doctrine\ORM\EntityManager;
use App\Repository\OutgoingRepository;
use App\Entity\Category;
use App\Enumeration\CategoryEnum;

final class OutgoingService
{

    private EntityManager $entityManager;

    private OutgoingRepository $outgoindRepository;

    private LoggerInterface $logger;

    private CategoryService $categoryService;

    public function __construct(ManagerRegistry $managerRegistry, LoggerInterface $looger, CategoryService $categoryService)
    {
        $this->entityManager = $managerRegistry->getManager();
        $this->logger = $looger;
        $this->outgoindRepository = $this->entityManager->getRepository(Outgoing::class);
        $this->categoryService = $categoryService;
    }

    public function save(Outgoing $outgoing): ?Outgoing
    {
        $this->logger->info('save - persistindo a despesa - despesa: ' . $outgoing->__toString());
        $this->verifyOutgoingDuplication($outgoing->getDescription(), $outgoing->getDate());
        
        $outgoing->setCategory($this->verifyCategory($outgoing->getCategory()));
        
        $this->entityManager->persist($outgoing);
        $this->entityManager->flush();

        return $outgoing;
    }

    public function getAll()
    {
        $this->logger->info('getAll - gerando lista de todas as despesas');
        return $this->outgoindRepository->findAll();
    }
    
    public function getAllByDescription($description) 
    {
        $this->logger->info('getAllByDescription - descricao: '.$description);
        $outgoings = $this->outgoindRepository->findByDescription($description);
        $this->logger->info('getAllByDescription - despesas: '. json_encode($outgoings));
        
        return $outgoings;
    }

    public function findById($id)
    {
        $this->logger->info('findById - id: ' . $id);
        $outgoing = $this->outgoindRepository->find($id);

        if ($outgoing == null) {
            $this->logger->error('findById - não foi encontrado despesa com id ' . $id);
            throw new \RuntimeException('não foi encontrado despesa com id ' . $id);
        }

        return $outgoing;
    }

    public function update($id, $outgoing)
    {
        $this->logger->info('update - despesa: ' . $outgoing->__toString());
        $outgoingOld = $this->findById($id);

        $outgoingOld->setDescription($outgoing->getDescription());
        $outgoingOld->setValue($outgoing->getValue());
        $outgoingOld->setDate($outgoing->getDate());

        return $this->save($outgoingOld);
    }

    public function deleteById(int $id)
    {
        $this->entityManager->remove($this->findById($id));
        $this->entityManager->flush();
    }

    public function getAllByMonth($month, $year)
    {
        $this->logger->info("getAllByMonth - mês: $month, ano: $year");
        $outgoings = $this->outgoindRepository->findByMoth($month, $year);
        $this->logger->info("getAllByMonth - despesas: ".json_encode($outgoings));
        
        return $outgoings;
    }
    
    private function verifyCategory(Category $category): Category
    {
        $this->logger->info('verifyCategory -> $category '.$category->__toString());
        $validateCategory = CategoryEnum::tryFrom($category->getName());

        if ($validateCategory == null) {
            throw new \RuntimeException("A categoria informada não é valida!");
        }

        return $this->categoryIsNull($category);
    }

    private function categoryIsNull($category)
    {
        $this->logger->info('categoryIsNull - $category '.$category->__toString());
        $categorySearch = $this->categoryService->findByName($category->getName());
        if ($categorySearch == null) {
            $this->logger->info('categoryIsNull - categoria é não existe');
            return $this->categoryService->save($category);
        }
        
        return $categorySearch;
    }

    private function verifyOutgoingDuplication($description, $date)
    {
        $this->logger->info('verifyOutgoingDuplication - description: ' . $description . ' mês: ' . $date->format('m'));
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