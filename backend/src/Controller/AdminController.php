<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/stats', name: 'api_admin_stats', methods: ['GET'])]
    public function stats(
        OrderRepository   $orderRepo,
        ProductRepository $productRepo,
        UserRepository    $userRepo,
    ): JsonResponse {
        // Total orders & revenue
        $ordersData = $orderRepo->createQueryBuilder('o')
            ->select('COUNT(o.id) as totalOrders, SUM(o.total) as totalRevenue')
            ->getQuery()
            ->getSingleResult();

        // Low stock products (quantity <= 5)
        $lowStock = $productRepo->createQueryBuilder('p')
            ->select('p.id, p.title, p.quantity')
            ->where('p.quantity <= :threshold')
            ->setParameter('threshold', 5)
            ->orderBy('p.quantity', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getArrayResult();

        // Total users
        $totalUsers = $userRepo->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Total products
        $totalProducts = $productRepo->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Recent orders (last 5)
        $recentOrders = $orderRepo->createQueryBuilder('o')
            ->select('o.id, o.total, o.status, o.createdAt')
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getArrayResult();

        return $this->json([
            'totalOrders'   => (int) $ordersData['totalOrders'],
            'totalRevenue'  => (float) ($ordersData['totalRevenue'] ?? 0),
            'totalUsers'    => (int) $totalUsers,
            'totalProducts' => (int) $totalProducts,
            'lowStock'      => $lowStock,
            'recentOrders'  => array_map(fn($o) => [
                'id'        => $o['id'],
                'total'     => $o['total'],
                'status'    => $o['status'] instanceof \BackedEnum ? $o['status']->value : $o['status'],
                'createdAt' => $o['createdAt'] instanceof \DateTimeInterface ? $o['createdAt']->format('d/m/Y H:i') : $o['createdAt'],
            ], $recentOrders),
        ]);
    }
}
