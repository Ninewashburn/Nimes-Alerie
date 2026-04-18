<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\OrderStatus;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminOrderController extends AbstractController
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/api/admin/orders', name: 'api_admin_orders_list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function list(Request $request): JsonResponse
    {
        $page = max(1, min(10000, (int) $request->query->get('page', 1)));
        $itemsPerPage = max(1, min(100, (int) $request->query->get('itemsPerPage', 20)));
        $offset = ($page - 1) * $itemsPerPage;

        $orders = $this->orderRepository->findBy([], ['id' => 'DESC'], $itemsPerPage, $offset);
        $total = $this->orderRepository->count([]);

        $data = array_map(static function ($order) {
            $user = $order->getUser();
            $bill = $order->getBill();

            return [
                'id' => $order->getId(),
                'status' => $order->getStatus()->value,
                'total' => $order->getTotal(),
                'items' => $order->getItems(),
                'createdAt' => $order->getCreatedAt()?->format('d/m/Y H:i'),
                'billNumber' => $bill?->getNumber(),
                'payment' => $bill?->getPayment()?->value,
                'user' => $user ? [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                ] : null,
            ];
        }, $orders);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
            'orders' => $data,
        ]);
    }

    #[Route('/api/admin/orders/{id}/status', name: 'api_admin_order_status', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['status'])) {
            return $this->json(['error' => 'Missing "status" field'], 400);
        }

        $statusValue = $data['status'];
        $newStatus = OrderStatus::tryFrom($statusValue);

        if (null === $newStatus) {
            $validValues = array_map(static fn (OrderStatus $s) => $s->value, OrderStatus::cases());

            return $this->json([
                'error' => \sprintf('Invalid status "%s"', $statusValue),
                'validValues' => $validValues,
            ], 400);
        }

        $order->setStatus($newStatus);
        $this->em->flush();

        $user = $order->getUser();
        $bill = $order->getBill();

        return $this->json([
            'id' => $order->getId(),
            'status' => $order->getStatus()->value,
            'total' => $order->getTotal(),
            'items' => $order->getItems(),
            'createdAt' => $order->getCreatedAt()?->format('d/m/Y H:i'),
            'billNumber' => $bill?->getNumber(),
            'payment' => $bill?->getPayment()?->value,
            'user' => $user ? [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ] : null,
        ]);
    }
}
