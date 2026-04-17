<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use BackedEnum;
use DateTimeInterface;

class AdminStatsService
{
    public function __construct(
        private readonly OrderRepository $orderRepo,
        private readonly ProductRepository $productRepo,
        private readonly UserRepository $userRepo,
    ) {
    }

    /**
     * @return array{
     *   totalOrders: int,
     *   totalRevenue: float,
     *   totalUsers: int,
     *   totalProducts: int,
     *   lowStock: array<array{id: int, title: string, quantity: int}>,
     *   recentOrders: array<array{id: int, total: string, status: string, createdAt: string}>
     * }
     */
    public function getStats(): array
    {
        $ordersData = $this->orderRepo->createQueryBuilder('o')
            ->select('COUNT(o.id) as totalOrders, SUM(o.total) as totalRevenue')
            ->getQuery()
            ->getSingleResult();

        $lowStock = $this->productRepo->createQueryBuilder('p')
            ->select('p.id, p.title, p.quantity')
            ->where('p.quantity <= :threshold')
            ->setParameter('threshold', 5)
            ->orderBy('p.quantity', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getArrayResult();

        $totalUsers = $this->userRepo->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $totalProducts = $this->productRepo->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $recentOrders = $this->orderRepo->createQueryBuilder('o')
            ->select('o.id, o.total, o.status, o.createdAt')
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getArrayResult();

        return [
            'totalOrders' => (int) $ordersData['totalOrders'],
            'totalRevenue' => (float) ($ordersData['totalRevenue'] ?? 0),
            'totalUsers' => (int) $totalUsers,
            'totalProducts' => (int) $totalProducts,
            'lowStock' => $lowStock,
            'recentOrders' => array_map(static fn ($o) => [
                'id' => $o['id'],
                'total' => $o['total'],
                'status' => $o['status'] instanceof BackedEnum ? $o['status']->value : $o['status'],
                'createdAt' => $o['createdAt'] instanceof DateTimeInterface
                    ? $o['createdAt']->format('d/m/Y H:i')
                    : $o['createdAt'],
            ], $recentOrders),
        ];
    }
}
