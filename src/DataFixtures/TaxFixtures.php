<?php

namespace App\DataFixtures;

use App\Entity\Tax;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaxFixtures extends Fixture
{
    public const TAX_REFERENCE = 'tax-';

    public function load(ObjectManager $manager)
    {
        $taxsConfig = [
            [
                'name' => 'IVA normal',
                'percent' => 21,
            ], [
                'name' => 'IVA reducido',
                'percent' => 10,
            ]
            , [
                'name' => 'IVA super reducido',
                'percent' => 4,
            ]
        ];

        $i = 0;
        foreach ($taxsConfig as $taxConfig) {
            $tax = $this->createTax($taxConfig);
            $manager->persist($tax);
            $this->addReference(self::TAX_REFERENCE.$i, $tax);
            $i++;
        }

        $manager->flush();
    }
    private function createTax($taxConfig)
    {
        $tax = new Tax();
        $tax->setName($taxConfig['name']);
        $tax->setPercent($taxConfig['percent']);

        return $tax;
    }
}
