<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class OrderServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject $em;
    private ProductRepository&MockObject $productRepo;
    private MailerInterface&MockObject $mailer;
    private OrderService $orderService;

    protected function setUp(): void
    {
        $this->em          = $this->createMock(EntityManagerInterface::class);
        $this->productRepo = $this->createMock(ProductRepository::class);
        $this->mailer      = $this->createMock(MailerInterface::class);
        $this->orderService = new OrderService($this->em, $this->productRepo, $this->mailer);

        $this->em->method('wrapInTransaction')
            ->willReturnCallback(fn (callable $fn) => $fn());
    }

    public function testCreateOrderThrowsOnEmptyItems(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cart is empty');

        $this->orderService->createOrder(
            user: new User(),
            items: [],
            deliveryAddress: '1 rue test',
            deliveryCity: 'Paris',
            deliveryPostal: '75001',
            deliveryCountry: 'France',
            paymentMethod: 'card',
        );
    }

    public function testCreateOrderThrowsOnIncompleteAddress(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Delivery address is incomplete');

        $this->orderService->createOrder(
            user: new User(),
            items: [['productId' => 1, 'quantity' => 1]],
            deliveryAddress: '',
            deliveryCity: 'Paris',
            deliveryPostal: '75001',
            deliveryCountry: 'France',
            paymentMethod: 'card',
        );
    }

    public function testCreateOrderThrowsWhenProductNotFound(): void
    {
        $this->productRepo->method('find')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product #99 not found');

        $this->orderService->createOrder(
            user: new User(),
            items: [['productId' => 99, 'quantity' => 1]],
            deliveryAddress: '1 rue test',
            deliveryCity: 'Paris',
            deliveryPostal: '75001',
            deliveryCountry: 'France',
            paymentMethod: 'card',
        );
    }

    public function testCreateOrderThrowsOnInsufficientStock(): void
    {
        $product = new Product();
        $product->setTitle('Croquettes Galactiques');
        $product->setQuantity(2);
        $product->setPriceTTC('19.99');

        $this->productRepo->method('find')->willReturn($product);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessageMatches('/Stock insuffisant/');

        $this->orderService->createOrder(
            user: new User(),
            items: [['productId' => 1, 'quantity' => 10]],
            deliveryAddress: '1 rue test',
            deliveryCity: 'Paris',
            deliveryPostal: '75001',
            deliveryCountry: 'France',
            paymentMethod: 'card',
        );
    }

    public function testCreateOrderDecrementsStock(): void
    {
        $product = new Product();
        $product->setTitle('Croquettes Galactiques');
        $product->setQuantity(5);
        $product->setPriceTTC('19.99');

        $this->productRepo->method('find')->willReturn($product);
        $this->em->expects($this->atLeastOnce())->method('persist');

        $order = $this->orderService->createOrder(
            user: new User(),
            items: [['productId' => 1, 'quantity' => 3]],
            deliveryAddress: '1 rue test',
            deliveryCity: 'Paris',
            deliveryPostal: '75001',
            deliveryCountry: 'France',
            paymentMethod: 'card',
        );

        $this->assertSame(2, $product->getQuantity());
        $this->assertSame('59.97', $order->getTotal());
    }

    public function testCreateOrderCalculatesTotalServerSide(): void
    {
        $product = new Product();
        $product->setTitle('Laisse Spatiale');
        $product->setQuantity(10);
        $product->setPriceTTC('9.50');

        $this->productRepo->method('find')->willReturn($product);
        $this->em->method('persist');

        $order = $this->orderService->createOrder(
            user: new User(),
            items: [['productId' => 1, 'quantity' => 4]],
            deliveryAddress: '42 avenue de la Lune',
            deliveryCity: 'Nîmes',
            deliveryPostal: '30000',
            deliveryCountry: 'France',
            paymentMethod: 'card',
        );

        // 4 × 9.50 = 38.00
        $this->assertSame('38', $order->getTotal());
    }

    public function testCreateOrderThrowsOnInvalidItem(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid item');

        $this->orderService->createOrder(
            user: new User(),
            items: [['productId' => 0, 'quantity' => 1]],
            deliveryAddress: '1 rue test',
            deliveryCity: 'Paris',
            deliveryPostal: '75001',
            deliveryCountry: 'France',
            paymentMethod: 'card',
        );
    }
}
