<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoutesTest extends WebTestCase
{
    /**
     * @dataProvider provideUrlsMethods
     */
    public function testRouteIsSuccessful($method, $url, $status)
    {
        /** La definición del HHTP_HOST debería establecerse en una variable de entorno
         *  Aquí estamos usando el servidor interno de symfony
         */
        $client = self::createClient([], [
            'HTTP_HOST' => '127.0.0.1:8000',
        ]);
        $client->request($method, $url);

        $this->assertEquals($status, $client->getResponse()->getStatusCode());
    }

    public function provideUrlsMethods()
    {
        return [
            ['GET','/API/v1/tax/list', 200],
            ['GET','/API/v1/product/list', 200],
            // ['GET','/API/v1/product/new', 405],
            ['POST','/API/v1/product/new', 422],
        ];
    }
}
