<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private ProductRepository&MockObject $productRepository;
    private ProductService $productService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->productService = new ProductService($this->entityManager, $this->productRepository);
    }

    public function testGetAllSortedByTitle(): void
    {
        $products = [new Product(), new Product()];
        $this->productRepository
            ->expects($this->once())
            ->method('findBy')
            ->with([], ['title' => 'ASC'])
            ->willReturn($products);

        $result = $this->productService->getAllSortedByTitle();

        $this->assertCount(2, $result);
        $this->assertSame($products, $result);
    }

    public function testFindById(): void
    {
        $product = new Product();
        $this->productRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($product);

        $result = $this->productService->findById(1);

        $this->assertSame($product, $result);
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $this->productRepository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $result = $this->productService->findById(999);

        $this->assertNull($result);
    }

    public function testSave(): void
    {
        $product = new Product();

        $this->entityManager->expects($this->once())->method('persist')->with($product);
        $this->entityManager->expects($this->once())->method('flush');

        $this->productService->save($product);
    }

    public function testDelete(): void
    {
        $product = new Product();

        $this->entityManager->expects($this->once())->method('remove')->with($product);
        $this->entityManager->expects($this->once())->method('flush');

        $this->productService->delete($product);
    }
}
