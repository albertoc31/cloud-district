<?php

namespace App\Controller;

use App\Entity\Tax;
use App\Repository\TaxRepository;
use App\Service\GeneratePaginatedTaxList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/API/v1/tax")
 */
class ApiTaxController extends AbstractController
{
    /**
     * @Route("/list", name="api_tax_index", methods={"GET"})
     */
    public function index(TaxRepository $taxRepository): JsonResponse
    {
        $tax_collection= $taxRepository->findAll();

        $tax_array = [];

        /** @var Tax $tax */
        foreach ($tax_collection as $tax) {
            $tax_array[$tax->getId()] = $tax->__toString();
        }
        return new JsonResponse($tax_array, 200);
    }
}
