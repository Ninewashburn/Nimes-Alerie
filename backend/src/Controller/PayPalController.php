<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\OrderService;
use App\Service\PayPalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PayPalController extends AbstractController
{
    public function __construct(
        private readonly PayPalService $paypalService,
        private readonly OrderService $orderService,
    ) {}

    /**
     * Step 1 of the PayPal flow: create a PayPal order and return its ID to the frontend.
     * The frontend passes this ID to the PayPal JS SDK to open the approval popup.
     */
    #[Route('/api/paypal/create-order', name: 'api_paypal_create_order', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function createOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $amount = (float) ($data['amount'] ?? 0);

        if ($amount <= 0) {
            return $this->json(['error' => 'Montant invalide'], 400);
        }

        try {
            $paypalOrderId = $this->paypalService->createOrder($amount);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Service PayPal indisponible: ' . $e->getMessage()], 502);
        }

        return $this->json(['paypalOrderId' => $paypalOrderId]);
    }

    /**
     * Step 2 of the PayPal flow: capture the approved payment and create the local order.
     * Called by the frontend after the user approves the payment in the PayPal popup.
     */
    #[Route('/api/paypal/capture-order', name: 'api_paypal_capture_order', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function captureOrder(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $paypalOrderId = $data['paypalOrderId'] ?? null;
        if (!$paypalOrderId) {
            return $this->json(['error' => 'paypalOrderId manquant'], 400);
        }

        try {
            $capture = $this->paypalService->captureOrder($paypalOrderId);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Capture échouée: ' . $e->getMessage()], 502);
        }

        if (($capture['status'] ?? '') !== 'COMPLETED') {
            return $this->json(['error' => 'Paiement non complété (statut: ' . ($capture['status'] ?? 'inconnu') . ')'], 400);
        }

        try {
            $order = $this->orderService->createOrder(
                user: $user,
                items: $data['items'] ?? [],
                deliveryAddress: $data['deliveryAddress'] ?? '',
                deliveryCity: $data['deliveryCity'] ?? '',
                deliveryPostal: $data['deliveryPostalCode'] ?? '',
                deliveryCountry: $data['deliveryCountry'] ?? 'France',
                paymentMethod: 'paypal',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        }

        return $this->json([
            'id'          => $order->getId(),
            'billNumber'  => $order->getBill()?->getNumber(),
            'status'      => $order->getStatus()->value,
            'total'       => $order->getTotal(),
            'itemsCount'  => array_sum(array_column($order->getItems() ?? [], 'quantity')),
            'createdAt'   => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'payment'     => 'paypal',
        ], 201);
    }
}
