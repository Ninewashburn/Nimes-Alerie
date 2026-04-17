<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findArticlesByName(string $name): array
    {
        return $this->createQueryBuilder('p')
            ->select('product.name')
            ->where('product.name LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @param Collection $products
    //     * @param int $limit
    //     * @return array
    //     */
    //    public function findRelatedProductByCategory(Collection $products, int $limit = 5): array
    //    {
    //        return $this->createQueryBuilder('game')
    //            ->select('product', 'categories')
    //            ->join('product.category', 'categories')
    //            ->where('categories IN(:categories)')
    //            ->setParameter('categories', $products)
    //            ->orderBy('product.title', 'DESC')
    //            ->setMaxResults($limit)
    //            ->getQuery()
    //            ->getResult();
    //    }

    //    /**
    //     * @param int $limit
    //     * @param bool $isOrderedByName
    //     * @return array
    //     */
    //    public function findLastGames(int $limit = 10, bool $isOrderedByName = false): array {
    //        // SELECT * FROM game JOIN sur language & genre
    //        $qb = $this->createQueryBuilder('product');
    //
    //        // En fonction des conditions, j'ajoute différent ORDER BY sur ma requête
    //        if ($isOrderedByName) {
    //            $qb->orderBy('product.title', 'ASC');
    //        } else {
    //            $qb->orderBy('product.title', 'DESC');
    //        }
    //
    //        // LIMIT 10 ou LIMIT $limit
    //        return $qb->setMaxResults($limit)
    //            ->getQuery()
    //            ->getResult()
    //            ;
    //    }

    /**
     * Return all product name.
     */
    public function findAllNames(string $title): array
    {
        // FROM product AS product
        return $this->createQueryBuilder('product')
            // SELECT product.title, product.slug
            ->select('product.title')
            // WHERE product.title LIKE '%$name%' => $name est un paramètre
            ->where('product.title LIKE :title')
            ->setParameter('title', '%'.$title.'%')
            ->getQuery()
            ->getResult()
        ;
    }
    //    // /**
    //    //  * @return Product[] Returns an array of Product objects
    //    //  */
    //    /*
    //    public function findByExampleField($value)
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    //    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
