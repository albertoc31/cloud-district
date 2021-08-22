<?php

namespace App\Tests\Service\Validators;

use App\Entity\Tax;
use App\Service\Validators\ValidateDataNewProduct;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ValidateDataNewProductTest extends KernelTestCase
{
    public function testInvokeOK(): void
    {
        $testData = $this->getTestData(true);

        self::bootKernel();

        $container = static::getContainer();

        $validateDataNewProduct = $container->get(ValidateDataNewProduct::class);
        $errors = $validateDataNewProduct($testData['data'], $testData['taxChoices']);

        // var_dump($errors);die();

        $this->assertCount(0, $errors);
    }

    public function testInvokeKO(): void
    {
        $testData = $this->getTestData(false);

        self::bootKernel();

        $container = static::getContainer();

        $validateDataNewProduct = $container->get(ValidateDataNewProduct::class);
        $errors = $validateDataNewProduct($testData['data'], $testData['taxChoices']);

        $this->assertCount(4, $errors);
    }

    public function getTestData ($isOK)
    {
        $tax1 = $this->createMock(Tax::class);
        $tax1->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $tax2 = $this->createMock(Tax::class);
        $tax2->expects($this->any())
            ->method('getId')
            ->willReturn(2);
        $tax3 = $this->createMock(Tax::class);
        $tax3->expects($this->any())
            ->method('getId')
            ->willReturn(3);

        $testData = [
            'data' => [
                'name' => $isOK ? 'new product' : '',
                'description' => $isOK ? 'new product description' : [],
                'price' => $isOK ? 10 : 'diez',
                'tax' => $isOK ? 2 : 8,
            ],
            'taxChoices' => [$tax1,$tax2,$tax3]
        ];
        return $testData;
    }
}
