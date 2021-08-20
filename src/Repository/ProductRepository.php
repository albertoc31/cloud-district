<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function paginate($dql, $page, $limit)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }

    /**
     * Creamos el metodo para devolver todos los productos paginados
     *
     * @param int $currentPage
     * @param int $limit
     * @return array
     */
    public function getPaginatedProducts($filter = '', $currentPage = 1, $limit = Product::MAX_LIST_PRODUCT, $ordering = [])
    {
        // Create our query
        $query = $this->createQueryBuilder('product');

        if ($filter) {
            $query->andWhere('product.name LIKE :filter')
                ->setParameter('filter', '%'.$filter.'%');
        }

        $query->orderBy('product.' . $ordering['by'], $ordering['order'])
            ->getQuery();

        $paginatedProducts = $this->paginate($query, $currentPage, $limit);

        return $paginatedProducts;
    }
}
