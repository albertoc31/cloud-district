<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Tax;
use App\Service\Validators\ValidateDataNewProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private $manager;
    private $validateDataNewProduct;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager, ValidateDataNewProduct $validateDataNewProduct )
    {
        parent::__construct($registry, Product::class );
        $this->manager = $manager;
        $this->validateDataNewProduct = $validateDataNewProduct;
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
     * Metodo para devolver todos los productos paginados
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

    /**
     * Metodo para agregar producto nuevo
     *
     * @param $data
     * @return Product|bool
     */
    public function saveProduct($data)
    {
        $taxRepository = $this->manager->getRepository('App\Entity\Tax');
        $taxList = $taxRepository->findAll();

        /* Validamos la query */
        $errors = $this->validateDataNewProduct->__invoke($data, $taxList);
        if (0 !== count($errors)) {
            $errorMessages = [];
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ": " . $error->getMessage();
            }
            return $errorMessages;
        }

        /** @var Tax $tax */
        $tax = $taxRepository->find($data['tax']);

        if (! $tax instanceof Tax) {
         return false;
        }

        /** Podemos hacer directamente este calculo porque traemos los datos sanitizados */
        $priceWithTax = $data['price'] * ($tax->getPercent()/100 + 1);

        $newProduct = new Product();

        $newProduct
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setPrice($data['price'])
            ->setTax($tax)
            ->setPriceWithTax($priceWithTax);

        $this->manager->persist($newProduct);
        // $this->manager->flush();

        return $newProduct;
    }
}
