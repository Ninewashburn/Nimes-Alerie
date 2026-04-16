<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Bill;
use App\Entity\Delivery;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\User;
use App\Enum\OrderStatus;
use App\Enum\PaymentMethod;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\LockMode;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProductRepository $productRepo,
        private readonly MailerInterface $mailer,
    ) {}

    /**
     * @param array<array{productId: int, quantity: int}> $items
     *
     * @throws \InvalidArgumentException when items are empty or address is incomplete
     * @throws \DomainException          when stock is insufficient (includes product title + quantities)
     */
    public function createOrder(
        User $user,
        array $items,
        string $deliveryAddress,
        string $deliveryCity,
        string $deliveryPostal,
        string $deliveryCountry,
        string $paymentMethod,
    ): Order {
        if (empty($items)) {
            throw new \InvalidArgumentException('Cart is empty');
        }

        if (empty($deliveryAddress) || empty($deliveryCity) || empty($deliveryPostal)) {
            throw new \InvalidArgumentException('Delivery address is incomplete');
        }

        $order = $this->em->wrapInTransaction(function () use (
            $user, $items, $deliveryAddress, $deliveryCity, $deliveryPostal, $deliveryCountry, $paymentMethod
        ): Order {
            $serverTotal   = 0.0;
            $enrichedItems = [];
            $totalQty      = 0;

            foreach ($items as $item) {
                $productId = (int) ($item['productId'] ?? 0);
                $requested = (int) ($item['quantity']  ?? 0);

                if ($productId <= 0 || $requested <= 0) {
                    throw new \InvalidArgumentException("Invalid item (id={$productId})");
                }

                // Pessimistic write lock: blocks concurrent reads-for-update on the same row.
                // Prevents two simultaneous orders from both passing the stock check.
                $product = $this->productRepo->find($productId, LockMode::PESSIMISTIC_WRITE);

                if (!$product) {
                    throw new \InvalidArgumentException("Product #{$productId} not found");
                }

                if (!$product->isActive()) {
                    throw new \DomainException(sprintf(
                        'Le produit "%s" n\'est plus disponible à la vente.',
                        $product->getTitle(),
                    ));
                }

                if ($product->getQuantity() < $requested) {
                    throw new \DomainException(sprintf(
                        'Stock insuffisant pour "%s" : %d disponible(s), %d demandé(s).',
                        $product->getTitle(),
                        $product->getQuantity(),
                        $requested,
                    ));
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

                $product->setQuantity($product->getQuantity() - $requested);
            }

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
            $bill->setNumber('BILL-'.strtoupper(uniqid()));
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

            return $order;
        });

        $this->sendConfirmationEmail($user, $order);

        return $order;
    }

    private function sendConfirmationEmail(User $user, Order $order): void
    {
        try {
            $items = $order->getItems() ?? [];
            $itemLines = implode("\n", array_map(
                fn (array $i) => sprintf('  - %s × %d : %.2f €', $i['title'], $i['quantity'], (float) $i['priceTTC'] * $i['quantity']),
                $items,
            ));

            $paymentLabel = match ($order->getBill()?->getPayment()?->value) {
                'paypal' => 'PayPal',
                default  => 'Carte bancaire',
            };

            $email = (new Email())
                ->from('noreply@nimes-alerie.gal')
                ->to((string) $user->getEmail())
                ->subject('Confirmation de votre commande ' . $order->getBill()?->getNumber())
                ->text(
                    "Bonjour {$user->getFirstName()},\n\n"
                    . "Votre commande a bien été enregistrée. Voici le récapitulatif :\n\n"
                    . "  Référence    : " . $order->getBill()?->getNumber() . "\n"
                    . "  Mode de paiement : {$paymentLabel}\n\n"
                    . "Articles commandés :\n{$itemLines}\n\n"
                    . "  TOTAL TTC : " . number_format((float) $order->getTotal(), 2, ',', ' ') . " €\n\n"
                    . "Merci pour votre confiance.\n"
                    . "L'équipe La Nîmes'Alerie"
                );

            $this->mailer->send($email);
        } catch (\Throwable) {
            // L'envoi d'email ne doit jamais bloquer la création de commande.
        }
    }
}
