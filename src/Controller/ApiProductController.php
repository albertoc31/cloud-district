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
    /**
     * @Route("/list/", name="api_product_index", methods={"GET"})
     */
    public function list(ProductRepository $productRepository, GeneratePaginatedEntityList $generatePaginatedEntityList, Request $request, ValidateQueryPaginatedProducts $validateQueryPaginatedProducts): JsonResponse
    {
        $filter = $request->query->get('filter');
        $page = $request->query->get('page') ?? 1;
        $limit = $request->query->get('limit') ?? Product::MAX_LIST_PRODUCT;
        $order_by = $request->query->get('order_by') ?? 'id';
        $order_dir = $request->query->get('order_dir') ?? 'ASC';

        /* Validamos la query */
        $errors = $validateQueryPaginatedProducts($filter, $page, $limit, $order_by, $order_dir);

        if (0 !== count($errors))
        {
            $errorMessages = [];
            /** @var ConstraintViolation $error */
            foreach ($errors as $error)
            {
                $errorMessages[] = "ERROR: " . $error->getMessage() . " " . $error->getPropertyPath();
            }
            return new JsonResponse($errorMessages, 422);
        }

        $ordering = [
            'by' => $order_by,
            'order' => $order_dir
        ];

        $paginatedProducts = $productRepository->getPaginatedProducts($filter, $page, $limit, $ordering);
        $listing = $generatePaginatedEntityList($paginatedProducts, $page, $limit);

        return new JsonResponse($listing, 200);
    }
}
