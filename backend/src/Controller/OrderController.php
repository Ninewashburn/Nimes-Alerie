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
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OrderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/api/my-orders', name: 'api_my_orders', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function myOrders(OrderRepository $orderRepo): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

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
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function createOrder(Request $request, ProductRepository $productRepo): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Invalid request body'], 400);
        }

        $items           = $data['items']              ?? [];
        $deliveryAddress = $data['deliveryAddress']    ?? '';
        $deliveryCity    = $data['deliveryCity']       ?? '';
        $deliveryPostal  = $data['deliveryPostalCode'] ?? '';
        $deliveryCountry = $data['deliveryCountry']    ?? 'France';
        $paymentMethod   = $data['paymentMethod']      ?? 'card';

        if (empty($items)) {
            return $this->json(['error' => 'Cart is empty'], 400);
        }

        if (empty($deliveryAddress) || empty($deliveryCity) || empty($deliveryPostal)) {
            return $this->json(['error' => 'Delivery address is incomplete'], 400);
        }

        // ── Vérification stock + calcul du total côté serveur ──────────────
        $serverTotal     = 0.0;
        $enrichedItems   = [];
        $totalQty        = 0;
        $productsToFlush = [];

        foreach ($items as $item) {
            $productId = (int) ($item['productId'] ?? 0);
            $requested = (int) ($item['quantity']  ?? 0);

            if ($productId <= 0 || $requested <= 0) {
                return $this->json(['error' => "Invalid item (id={$productId})"], 400);
            }

            $product = $productRepo->find($productId);

            if (!$product) {
                return $this->json(['error' => "Product #{$productId} not found"], 404);
            }

            if ($product->getQuantity() < $requested) {
                return $this->json([
                    'error' => sprintf(
                        'Stock insuffisant pour "%s" : %d disponible(s), %d demandé(s).',
                        $product->getTitle(),
                        $product->getQuantity(),
                        $requested,
                    ),
                ], 409);
            }

            $unitPrice    = (float) $product->getPriceTTC();
            $serverTotal += $unitPrice * $requested;
            $totalQty    += $requested;

            $enrichedItems[] = [
                'productId' => $productId,
                'title'     => $product->getTitle(),
                'quantity'  => $requested,
                'priceTTC'  => $product->getPriceTTC(),
            ];

            // Décrémentation du stock (flush groupé après validation complète)
            $product->setQuantity($product->getQuantity() - $requested);
            $productsToFlush[] = $product;
        }

        // ── Création de la commande ─────────────────────────────────────────
        $orderLine = new OrderLine();
        $orderLine->setQuantity($totalQty);

        $delivery = new Delivery();
        $delivery->setDeliveryAddress($deliveryAddress);
        $delivery->setDeliveryCity($deliveryCity);
        $delivery->setDeliveryPostalCode($deliveryPostal);
        $delivery->setDeliveryCountry($deliveryCountry);
        $delivery->setStatus('pending');
        $delivery->setOrderLine($orderLine);
        $orderLine->setDelivery($delivery);

        $bill = new Bill();
        $bill->setPayment(PaymentMethod::from($paymentMethod));
        $bill->setNumber('BILL-' . strtoupper(uniqid()));
        $bill->setCreatedAt(new \DateTimeImmutable());

        $order = new Order();
        $order->setStatus(OrderStatus::PENDING);
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setTotal((string) round($serverTotal, 2));
        $order->setItems($enrichedItems);
        $order->setOrderLine($orderLine);
        $order->setBill($bill);
        $order->setUser($user);

        $this->em->persist($orderLine);
        $this->em->persist($delivery);
        $this->em->persist($bill);
        $this->em->persist($order);
        $this->em->flush();

        return $this->json([
            'id'         => $order->getId(),
            'billNumber' => $bill->getNumber(),
            'status'     => $order->getStatus()->value,
            'total'      => $order->getTotal(),
            'itemsCount' => $totalQty,
            'createdAt'  => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'payment'    => $bill->getPayment()->value,
        ], 201);
    }
}
