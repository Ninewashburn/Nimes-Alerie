<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CartLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartLine>
 *
 * @method CartLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartLine|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method CartLine[]    findAll()
 * @method CartLine[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
class CartLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartLine::class);
    }

    // /**
 * @extends ServiceEntityRepository<CartLine>
 *
    //  * @return CartLine[] Returns an array of CartLine objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CartLine
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
