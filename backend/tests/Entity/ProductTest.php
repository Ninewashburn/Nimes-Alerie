<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $product = new Product();

        $product->setTitle('Test Product');
        $this->assertSame('Test Product', $product->getTitle());

        $product->setDescription('A test product');
        $this->assertSame('A test product', $product->getDescription());

        $product->setPriceHT('24.99');
        $this->assertSame('24.99', $product->getPriceHT());

        $product->setPriceTTC('29.99');
        $this->assertSame('29.99', $product->getPriceTTC());

        $product->setQuantity(10);
        $this->assertSame(10, $product->getQuantity());

        $product->setImage('image.jpg');
        $this->assertSame('image.jpg', $product->getImage());

        $product->setCover('cover.jpg');
        $this->assertSame('cover.jpg', $product->getCover());
    }

    public function testBrandRelation(): void
    {
        $product = new Product();
        $brand = new Brand();

        $product->setBrand($brand);
        $this->assertSame($brand, $product->getBrand());
    }

    public function testCategoryRelation(): void
    {
        $product = new Product();
        $category = new Category();

        $product->addCategory($category);
        $this->assertTrue($product->getCategory()->contains($category));

        $product->removeCategory($category);
        $this->assertFalse($product->getCategory()->contains($category));
    }
}
