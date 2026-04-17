<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Service\OrderService;
use DomainException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {
    }

    #[Route('/api/my-orders', name: 'api_my_orders', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function myOrders(OrderRepository $orderRepo): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $orders = $orderRepo->findBy(['user' => $user], ['id' => 'DESC']);

        return $this->json(array_map(static fn ($o) => [
            'id' => $o->getId(),
            'status' => $o->getStatus()->value,
            'total' => $o->getTotal(),
            'items' => $o->getItems(),
            'createdAt' => $o->getCreatedAt()?->format('d/m/Y'),
            'billNumber' => $o->getBill()?->getNumber(),
            'payment' => $o->getBill()?->getPayment()?->value,
        ], $orders));
    }

    #[Route('/api/orders', name: 'api_create_order', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function createOrder(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Invalid request body'], 400);
        }

        try {
            $order = $this->orderService->createOrder(
                user: $user,
                items: $data['items'] ?? [],
                deliveryAddress: $data['deliveryAddress'] ?? '',
                deliveryCity: $data['deliveryCity'] ?? '',
                deliveryPostal: $data['deliveryPostalCode'] ?? '',
                deliveryCountry: $data['deliveryCountry'] ?? 'France',
                paymentMethod: $data['paymentMethod'] ?? 'card',
            );
        } catch (InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        }

        return $this->json([
            'id' => $order->getId(),
            'billNumber' => $order->getBill()?->getNumber(),
            'status' => $order->getStatus()->value,
            'total' => $order->getTotal(),
            'itemsCount' => array_sum(array_column($order->getItems() ?? [], 'quantity')),
            'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'payment' => $order->getBill()?->getPayment()?->value,
        ], 201);
    }
}
