<?php

namespace App\Tests\Service\Validators;

use App\Service\Validators\ValidateQueryPaginatedProducts;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ValidateQueryPaginatedProductsTest extends KernelTestCase
{
    public function testInvokeOK(): void
    {
        $query = $this->getQuery(true);

        self::bootKernel();

        $container = static::getContainer();

        $validateQueryPaginatedProduct = $container->get(ValidateQueryPaginatedProducts::class);
        $errors = $validateQueryPaginatedProduct($query['filter'], $query['page'], $query['limit'], $query['order_by'], $query['order_dir']);

        $this->assertCount(0, $errors);
    }

    public function testInvokeKO(): void
    {
        $query = $this->getQuery(false);

        self::bootKernel();

        $container = static::getContainer();

        $validateQueryPaginatedProduct = $container->get(ValidateQueryPaginatedProducts::class);
        $errors = $validateQueryPaginatedProduct($query['filter'], $query['page'], $query['limit'], $query['order_by'], $query['order_dir']);

        $this->assertCount(5, $errors);
    }

    private function getQuery($isOK)
    {
        $query = [];
        $query['filter'] = $isOK ? 'de' : [];
        $query['page'] = $isOK ? 1 : 'primera';
        $query['limit'] = $isOK ? 2 : 'pocos';
        $query['order_by'] = $isOK ? 'name' : 'tax';
        $query['order_dir'] = $isOK ? 'ASC' : 'BLOB';
        return $query;
    }
}
