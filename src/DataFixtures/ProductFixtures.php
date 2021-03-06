<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Tax;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public const PRODUCT_REFERENCE = 'product-';

    public function load(ObjectManager $manager)
    {
        $productsConfig = [
            [
                'name' => 'Chicles de menta',
                'description' => 'Paquete de 1000 chicles de menta',
                'price' => 10,
                'price_with_tax' => 12.1

            ], [
                'name' => 'Caja de leche',
                'description' => 'Pack de de 50 botellas de leche',
                'price' => 150,
                'price_with_tax' => 165
            ], [
                'name' => 'Saco de arroz',
                'description' => 'Saco de arroz de 25 kg',
                'price' => 16,
                'price_with_tax' => 16.64
            ], [
                'name' => 'Chicles de fresa',
                'description' => 'Paquete de 1000 chicles de fresa',
                'price' => 10,
                'price_with_tax' => 12.1

            ], [
                'name' => 'Caja de yogures naturales',
                'description' => 'Pack de de 80 yogures naturales',
                'price' => 80,
                'price_with_tax' => 88
            ]
            , [
                'name' => 'Saco de garbanzos',
                'description' => 'Saco de garbanzos de 15 kg',
                'price' => 18,
                'price_with_tax' => 18.72
            ]
        ];

        $p = 0;
        $t = 0;
        foreach ($productsConfig as $productConfig) {
            $taxId = $this->getReference(TaxFixtures::TAX_REFERENCE . $t);
            $tax = $manager->getRepository(Tax::class)->find($taxId);

            $product = $this->createProduct($productConfig, $tax);

            $manager->persist($product);
            $this->addReference(self::PRODUCT_REFERENCE.$p, $product);
            if ($t < 2) {
                $t++;
            } else {
                $t = 0;
            }
            $p++;
        }

        $manager->flush();
    }
    private function createProduct($productConfig, $tax)
    {
        $product = new Product();
        $product->setName($productConfig['name']);
        $product->setDescription($productConfig['description']);
        $product->setPrice($productConfig['price']);
        $product->setTax($tax);
        $product->setPriceWithTax($productConfig['price_with_tax']);

        return $product;
    }

    public function getDependencies()
    {
        return array(
            TaxFixtures::class,
        );
    }
}
