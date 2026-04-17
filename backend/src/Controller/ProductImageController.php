<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProductImageController extends AbstractController
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5 MB

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/api/products/{id}/image', name: 'api_product_image_upload', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function upload(int $id, Request $request): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $file = $request->files->get('image');

        if (!$file) {
            return $this->json(['error' => 'No image file provided'], 400);
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (!\in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return $this->json([
                'error' => \sprintf(
                    'Invalid file type. Allowed types: %s',
                    implode(', ', self::ALLOWED_EXTENSIONS)
                ),
            ], 400);
        }

        if ($file->getSize() > self::MAX_SIZE_BYTES) {
            return $this->json(['error' => 'File size exceeds the 5 MB limit'], 400);
        }

        $filename = uniqid('product_', true).'.'.$extension;
        $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/products';

        $file->move($uploadDir, $filename);

        $relativePath = 'uploads/products/'.$filename;
        $product->setImage($relativePath);
        $this->em->flush();

        return $this->json(['image' => $relativePath]);
    }
}
