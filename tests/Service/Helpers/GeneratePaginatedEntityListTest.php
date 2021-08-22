<?php

namespace App\Tests;

use App\Entity\Product;
use App\Entity\Tax;
use App\Service\Helpers\GeneratePaginatedEntityList;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeneratePaginatedEntityListTest extends KernelTestCase
{

    public function testInvoke(): void
    {
        $paginatedEntity = $this->getProductArray();
        $page = 1;
        $limit = 2;

        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        // (3) run some service & test the result
        $generatePaginatedEntityList = $container->get(GeneratePaginatedEntityList::class);
        $listing = $generatePaginatedEntityList($paginatedEntity, $page, $limit);

        //var_dump($listing); die();

        $this->assertCount(2, $listing['records']);
        $this->assertEquals(1, $listing['maxPages']);
        $this->assertEquals(1, $listing['thisPage']);
        $this->assertEquals('producto 1', $listing['records'][0]['name']);
        $this->assertEquals('tax 2 (20%)', $listing['records'][1]['tax']);
    }


    private function getProductArray()
    {
        /** Esta forma me parece más sencilla, pero seremos ortodoxos y usaremos mock objects */
        /* $productA = new Product();
        $class = new \ReflectionClass(Product::class);
        $property = $class->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($productA, 666); */


        $productA = $this->createMock(Product::class);
        $productA->expects($this->any())
            ->method('toArray')
            ->willReturn([
                'id' => 1,
                'name' => 'producto 1',
                'description' => 'descripcion del producto 1',
                'price' => '10.00',
                'tax' => 'tax 1 (10%)'
                ]);

        $productB = $this->createMock(Product::class);
        $productB->expects($this->any())
            ->method('toArray')
            ->willReturn([
                'id' => 2,
                'name' => 'producto 2',
                'description' => 'descripcion del producto 2',
                'price' => '20.00',
                'tax' => 'tax 2 (20%)'
            ]);

        return $paginatedEntity = [
            0 => $productA,
            1 => $productB
        ];
    }
}
