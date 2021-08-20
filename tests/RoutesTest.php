<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoutesTest extends WebTestCase
{
    /**
     * @dataProvider provideUrlsMethods
     */
    public function testRouteIsSuccessful($method, $url)
    {
        /** La definición del HHTP_HOST debería establecerse en una variable de entorno */
        $client = self::createClient([], [
            'HTTP_HOST' => '127.0.0.1:8000',
        ]);
        $client->request($method, $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function provideUrlsMethods()
    {
        return [
            ['GET','/API/v1/tax/list'],
            ['GET','/API/v1/product/list/'],
            // ...
        ];
    }
}
