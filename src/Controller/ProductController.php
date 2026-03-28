<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\BrandRepository;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/product')]
class ProductController extends AbstractController
{
    public function __construct(
        private ProductService $productService,
        private BrandRepository $brandRepository,
    ) {}

    #[Route('/', name: 'product_index')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $this->productService->getAllSortedByTitle(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'product_new')]
    public function new(Request $request): Response
    {
        return $this->handleForm($request, new Product());
    }

    #[Route('/{title}', name: 'product_detail')]
    public function show(Product $product): Response
    {
        return $this->render('product/detail.html.twig', [
            'product' => $product,
            'brand' => $this->brandRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/edit/{title}', name: 'product_edit')]
    public function edit(Request $request, Product $product): Response
    {
        return $this->handleForm($request, $product, 'product/edit.html.twig');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/{title}', name: 'product_delete')]
    public function delete(Product $product): Response
    {
        $this->productService->delete($product);

        return $this->redirectToRoute('product_index');
    }

    private function handleForm(Request $request, Product $product, string $template = 'product/new.html.twig'): Response
    {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->productService->save($product);

            return $this->redirectToRoute('product_index');
        }

        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }
}
