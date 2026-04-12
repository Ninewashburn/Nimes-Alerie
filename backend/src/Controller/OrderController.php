<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Bill;
use App\Entity\Delivery;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\User;
use App\Enum\OrderStatus;
use App\Enum\PaymentMethod;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/api/my-orders', name: 'api_my_orders', methods: ['GET'])]
    public function myOrders(OrderRepository $orderRepo): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Authentication required'], 401);
        }

        $orders = $orderRepo->findBy(['user' => $user], ['id' => 'DESC']);

        return $this->json(array_map(fn(Order $o) => [
            'id'         => $o->getId(),
            'status'     => $o->getStatus()->value,
            'total'      => $o->getTotal(),
            'items'      => $o->getItems(),
            'createdAt'  => $o->getCreatedAt()?->format('d/m/Y'),
            'billNumber' => $o->getBill()?->getNumber(),
            'payment'    => $o->getBill()?->getPayment()?->value,
        ], $orders));
    }

    #[Route('/api/orders', name: 'api_create_order', methods: ['POST'])]
    public function createOrder(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Authentication required'], 401);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Invalid request body'], 400);
        }

        $items           = $data['items']           ?? [];
        $deliveryAddress = $data['deliveryAddress'] ?? '';
        $deliveryCity    = $data['deliveryCity']    ?? '';
        $deliveryPostal  = $data['deliveryPostalCode'] ?? '';
        $deliveryCountry = $data['deliveryCountry'] ?? 'France';
        $paymentMethod   = $data['paymentMethod']   ?? 'card';
        $total           = $data['total']           ?? 0;

        if (empty($items)) {
            return $this->json(['error' => 'Cart is empty'], 400);
        }

        if (empty($deliveryAddress) || empty($deliveryCity) || empty($deliveryPostal)) {
            return $this->json(['error' => 'Delivery address is incomplete'], 400);
        }

        // --- OrderLine (total quantity snapshot) ---
        $totalQty = array_sum(array_column($items, 'quantity'));
        $orderLine = new OrderLine();
        $orderLine->setQuantity($totalQty);

        // --- Delivery ---
        $delivery = new Delivery();
        $delivery->setDeliveryAddress($deliveryAddress);
        $delivery->setDeliveryCity($deliveryCity);
        $delivery->setDeliveryPostalCode($deliveryPostal);
        $delivery->setDeliveryCountry($deliveryCountry);
        $delivery->setStatus('pending');
        $delivery->setOrderLine($orderLine);
        $orderLine->setDelivery($delivery);

        // --- Bill ---
        $bill = new Bill();
        $bill->setPayment(PaymentMethod::from($paymentMethod));
        $bill->setNumber('BILL-' . strtoupper(uniqid()));
        $bill->setCreatedAt(new \DateTimeImmutable());

        // --- Order ---
        $order = new Order();
        $order->setStatus(OrderStatus::PENDING);
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setTotal((string) $total);
        $order->setItems($items);
        $order->setOrderLine($orderLine);
        $order->setBill($bill);
        $order->setUser($user);

        $this->em->persist($orderLine);
        $this->em->persist($delivery);
        $this->em->persist($bill);
        $this->em->persist($order);
        $this->em->flush();

        return $this->json([
            'id'          => $order->getId(),
            'billNumber'  => $bill->getNumber(),
            'status'      => $order->getStatus()->value,
            'total'       => $order->getTotal(),
            'itemsCount'  => $totalQty,
            'createdAt'   => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'payment'     => $bill->getPayment()->value,
        ], 201);
    }
}
