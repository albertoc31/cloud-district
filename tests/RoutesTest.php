<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

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
            //'HTTP_HOST' => '127.0.0.1:8000',
            //'HTTP_AUTHORIZATION' => 'Bearer admintoken'
        ]);

        $client->request($method, $url);

        $this->assertEquals($status, $client->getResponse()->getStatusCode());
    }

    public function testAuthorizationOK()
    {
        $client = self::createClient([], [
            'HTTP_AUTHORIZATION' => 'Bearer admintoken'
        ]);

        $client->request('POST', '/API/v1/product/new');
        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }

    public function testAuthorizationKO()
    {
        $client = self::createClient([], [
            'HTTP_AUTHORIZATION' => 'Bearer notAdmintoken'
        ]);

        $client->request('POST', '/API/v1/product/new');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function provideUrlsMethods()
    {
        return [
            ['GET','/API/v1/tax/list', 200],
            ['GET','/API/v1/product/list', 200],
            // ['GET','/API/v1/product/new', 405],
            ['POST','/API/v1/product/new', 401],
        ];
    }
}
