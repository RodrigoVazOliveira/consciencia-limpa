<?php
namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use App\Entity\Category;

final class CategoryService
{
    private $entityManager;
    private $categoryRepository;
    private $logger;

    public function __construct(ManagerRegistry $managerRegister, LoggerInterface $logger)
    {
        $this->entityManager = $managerRegister->getManager();
        $this->logger = $logger;
        $this->categoryRepository = $this->entityManager->getRepository(Category::class);
    }

    public function save(Category $category): ?Category
    {
        $this->logger->info('save - category '.$category->__toString());
        if ($this->verifyCategoryNoDuplicateByName($category->getName())) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $category;
        }
        
        return $category;
    }

    public function verifyCategoryNoDuplicateByName($name): bool
    {
        $this->logger->info('verifyCategoryNoDuplicateByName - $name '.$name);
        $category = $this->findByName($name);
        if ($category != null) {
            $this->logger->info('verifyCategoryNoDuplicateByName - duplicado!');
            return false;
        }
        
        $this->logger->info('verifyCategoryNoDuplicateByName - nao duplicado!');

        return true;
    }

    public function findByName($name): ?Category
    {
        $this->logger->info('findByName - $name '.$name);
        $category = $this->categoryRepository->findByName($name);
        $this->logger->info('findByName - categoria buscada: '.$category);
        return $category;
    }
}
