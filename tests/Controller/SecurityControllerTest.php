<?php

namespace Tests\App\Controller;

use App\Tests\TestBase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends TestBase
{
    public function testUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }
}
