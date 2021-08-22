<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Tax;


class ApiProductControllerTest extends WebTestCase
{
    public function testListOK()
    {
        $client = self::createClient([], [
            'HTTP_HOST' => '127.0.0.1:8000',
        ]);
        $client->request('GET', '/API/v1/product/list?filter=de&page=1&limit=3&order_by=name&order_dir=DESC');

        $this->assertResponseIsSuccessful();
    }

    public function testListKO()
    {
        $client = self::createClient([], [
            'HTTP_HOST' => '127.0.0.1:8000',
        ]);
        $client->request('GET', '/API/v1/product/list?page=-2&limit=dos&order_by=tt&order_dir=null');

        $this->assertEquals(422, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('This value should be positive', $client->getResponse()->getContent());
    }


    public function testNewOK(): void
    {
        $client = static::createClient();
        $newProduct = [];
        $newProduct['name'] = 'product test';
        $newProduct['description'] = 'product test description';
        $newProduct['price'] = '10';
        $newProduct['tax'] = 1;

        $client->request('POST', '/API/v1/product/new', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer admintoken',
        ], json_encode($newProduct));

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('"name":"product test"', $client->getResponse()->getContent());
        $this->assertStringContainsString('"tax":"IVA normal (21%)"', $client->getResponse()->getContent());
        $this->assertStringContainsString('"price_with_tax":12.1', $client->getResponse()->getContent());
    }

    public function testNewKO(): void
    {
        $client = static::createClient();
        $newProduct = [];
        $newProduct['name'] = '';
        $newProduct['description'] = 'product test description';
        $newProduct['price'] = null;
        $newProduct['tax'] = null;

        $client->request('POST', '/API/v1/product/new', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer admintoken',
        ], json_encode($newProduct));

        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }

    public function testNewBadTax(): void
    {
        $client = static::createClient();
        $newProduct = [];
        $newProduct['name'] = 'product test';
        $newProduct['description'] = 'product test description';
        $newProduct['price'] = '10';
        $newProduct['tax'] = 7;

        $client->request('POST', '/API/v1/product/new', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer admintoken',
        ], json_encode($newProduct));

        $this->assertStringContainsString('[tax]: The value you selected is not a valid choice."', $client->getResponse()->getContent());
        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }
}
