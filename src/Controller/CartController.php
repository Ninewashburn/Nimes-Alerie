<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, ProductRepository $productsRepository): Response
    {
        $panier = $request->getSession()->get("panier", []);

        $dataPanier = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $product = $productsRepository->find($id);
            $dataPanier[] = [
                "produit" => $product,
                "quantite" => $quantite
            ];
            $total += $product->getPrice() * $quantite;
        }

        return $this->render('cart/index.html.twig', compact("dataPanier", "total"));
    }

    #[Route('/add/{id}', name: 'add')]
    public function add(Product $product, Request $request): Response
    {
        $session = $request->getSession();
        $panier = $session->get("panier", []);
        $id = $product->getId();

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set("panier", $panier);

        return $this->redirectToRoute("cart_index");
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Product $product, Request $request): Response
    {
        $session = $request->getSession();
        $panier = $session->get("panier", []);
        $id = $product->getId();

        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }

        $session->set("panier", $panier);

        return $this->redirectToRoute("cart_index");
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Product $product, Request $request): Response
    {
        $session = $request->getSession();
        $panier = $session->get("panier", []);
        $id = $product->getId();

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set("panier", $panier);

        return $this->redirectToRoute("cart_index");
    }

    #[Route('/delete', name: 'delete_all')]
    public function deleteAll(Request $request): Response
    {
        $request->getSession()->remove("panier");

        return $this->redirectToRoute("cart_index");
    }
}
