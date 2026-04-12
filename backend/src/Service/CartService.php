<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository,
    ) {}

    /**
     * @return array{items: array<int, array{produit: \App\Entity\Product, quantite: int}>, total: float}
     */
    public function getCartData(): array
    {
        $panier = $this->getCart();
        $dataPanier = [];
        $total = 0.0;

        foreach ($panier as $id => $quantite) {
            $product = $this->productRepository->find($id);
            if ($product !== null) {
                $dataPanier[] = [
                    'produit' => $product,
                    'quantite' => $quantite,
                ];
                $total += (float) $product->getPriceTTC() * $quantite;
            }
        }

        return ['items' => $dataPanier, 'total' => $total];
    }

    public function addProduct(int $productId): void
    {
        $panier = $this->getCart();

        if (!empty($panier[$productId])) {
            $panier[$productId]++;
        } else {
            $panier[$productId] = 1;
        }

        $this->saveCart($panier);
    }

    public function removeProduct(int $productId): void
    {
        $panier = $this->getCart();

        if (!empty($panier[$productId])) {
            if ($panier[$productId] > 1) {
                $panier[$productId]--;
            } else {
                unset($panier[$productId]);
            }
        }

        $this->saveCart($panier);
    }

    public function deleteProduct(int $productId): void
    {
        $panier = $this->getCart();
        unset($panier[$productId]);
        $this->saveCart($panier);
    }

    public function clearCart(): void
    {
        $this->requestStack->getSession()->remove('panier');
    }

    /**
     * @return array<int, int>
     */
    private function getCart(): array
    {
        return $this->requestStack->getSession()->get('panier', []);
    }

    /**
     * @param array<int, int> $cart
     */
    private function saveCart(array $cart): void
    {
        $this->requestStack->getSession()->set('panier', $cart);
    }
}
