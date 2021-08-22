<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\Helpers\GeneratePaginatedEntityList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Validator\ConstraintViolation;

use App\Service\Validators\ValidateQueryPaginatedProducts;

/**
 * @Route("/API/v1/product")
 */
class ApiProductController extends AbstractController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/list", name="api_product_index", methods={"GET"})
     */
    public function list(GeneratePaginatedEntityList $generatePaginatedEntityList, Request $request, ValidateQueryPaginatedProducts $validateQueryPaginatedProducts): JsonResponse
    {
        $filter = $request->query->get('filter') ?? '';
        /** Forzamos que page y limit sean enteros positivos.  */
        $page = $request->query->get('page') ? (int) $request->query->get('page') : 1;
        $limit = $request->query->get('limit') ? (int) $request->query->get('limit') : Product::MAX_LIST_PRODUCT;
        $order_by = $request->query->get('order_by') ?? 'id';
        $order_dir = $request->query->get('order_dir') ?? 'ASC';

        /* Validamos la query */
        $errors = $validateQueryPaginatedProducts($filter, $page, $limit, $order_by, $order_dir);

        //var_dump($errors);die();

        if (0 !== count($errors))
        {
            $errorMessages = [];
            /** @var ConstraintViolation $error */
            foreach ($errors as $error)
            {
                $errorMessages[] = $error->getPropertyPath() . ": " . $error->getMessage();
            }

            $response = [
                'success' => false,
                'status' => 422,
                'errorMessages' => $errorMessages,
                'version' => '1.0',
                'responseData' => [],
            ];

            return new JsonResponse($response, $response['status']);
        }

        $ordering = [
            'by' => $order_by,
            'order' => $order_dir
        ];

        $paginatedProducts = $this->productRepository->getPaginatedProducts($filter, $page, $limit, $ordering);
        $listing = $generatePaginatedEntityList($paginatedProducts, $page, $limit);

        $response = [
            'success' => true,
            'status' => 200,
            'errorMessages' => '',
            'version' => '1.0',
            'responseData' => $listing,
            ];

        return new JsonResponse($response, $response['status']);
    }

    /**
     * @Route("/new", name="api_product_new", methods={"POST"})
     */
    public function add (Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data))
        {
            $response = [
                'success' => false,
                'status' => 422,
                'errorMessages' => 'No data provided',
                'version' => '1.0',
                'responseData' => [],
            ];
            return new JsonResponse($response, $response['status']);
        }

        $newProduct = $this->productRepository->saveProduct($data);

        if (! $newProduct instanceof Product) {
            $response = [
                'success' => false,
                'status' => 422,
                'errorMessages' => $newProduct,
                'version' => '1.0',
                'responseData' => [],
            ];

            return new JsonResponse($response, $response['status']);
        }

        $response = [
            'success' => true,
            'status' => 200,
            'errorMessages' => '',
            'version' => '1.0',
            'responseData' => $newProduct->toArray(),
        ];

        return new JsonResponse($response, $response['status']);
    }
}
