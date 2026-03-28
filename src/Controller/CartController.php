<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    public function __construct(
        private CartService $cartService,
    ) {}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $cartData = $this->cartService->getCartData();

        return $this->render('cart/index.html.twig', [
            'dataPanier' => $cartData['items'],
            'total' => $cartData['total'],
        ]);
    }

    #[Route('/add/{id}', name: 'add')]
    public function add(Product $product): Response
    {
        $this->cartService->addProduct($product->getId());

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Product $product): Response
    {
        $this->cartService->removeProduct($product->getId());

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Product $product): Response
    {
        $this->cartService->deleteProduct($product->getId());

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/delete', name: 'delete_all')]
    public function deleteAll(): Response
    {
        $this->cartService->clearCart();

        return $this->redirectToRoute('cart_index');
    }
}
