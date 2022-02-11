<?php

namespace App\Tests\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CategoryServiceTest extends TestCase {

    public function testSaveWithDuplicate(): void {
        $category = new Category();

        $category->setName('Alimentação');

        $managerRegister = $this->createMock(ManagerRegistry::class);
        $logger = $this->createMock(LoggerInterface::class);
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $objectManager = $this->createMock(ObjectManager::class);

        $managerRegister->method('getManager')->willReturn($objectManager);
        $objectManager->method('getRepository')->willReturn($categoryRepository);

        $categoryRepository->method('findByName')->willReturn($category);

        $categoryService = new CategoryService($managerRegister, $logger);
        $categoryResult = $categoryService->save($category);

        $this->assertEquals(null, $categoryResult->getId());
        $this->assertEquals('Alimentação', $categoryResult->getName());
    }

    public function testSaveWithSuccess(): void {
        $category = new Category();
        $category->setId(1);
        $category->setName('Alimentação');

        $managerRegister = $this->createMock(ManagerRegistry::class);
        $logger = $this->createMock(LoggerInterface::class);
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $objectManager = $this->createMock(ObjectManager::class);

        $managerRegister->method('getManager')->willReturn($objectManager);
        $objectManager->method('getRepository')->willReturn($categoryRepository);

        $categoryRepository->method('findByName')->willReturn(null);

        $categoryService = new CategoryService($managerRegister, $logger);
        $categoryResult = $categoryService->save($category);

        $this->assertEquals(1, $categoryResult->getId());
        $this->assertEquals('Alimentação', $categoryResult->getName());
    }

}
