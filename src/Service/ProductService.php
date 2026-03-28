<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
    ) {}

    /**
     * @return Product[]
     */
    public function getAllSortedByTitle(): array
    {
        return $this->productRepository->findBy([], ['title' => 'ASC']);
    }

    public function findById(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function delete(Product $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }
}
