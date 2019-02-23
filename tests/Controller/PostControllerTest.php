<?php

namespace Tests\App\Controller;

use App\Entity\Post;
use App\Tests\TestBase;
use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends TestBase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/');
        $this->assertEquals(1, $crawler->filter('h1')->count());
        $this->assertContains('Blog', $crawler->filter('title')->text());
        $this->assertEquals('App\Controller\PostController::index', $client->getRequest()->attributes->get('_controller'));
    }

    public function testShow(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $slug = $em
            ->getRepository(Post::class)
            ->findOneBy(['id' =>1])->getSlug();
        $crawler = $client->request('GET', "/en/post/{$slug}");
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            1,
            $crawler->filter('h1')->count()
        );
        $this->assertEquals('App\Controller\PostController::show', $client->getRequest()->attributes->get('_controller'));
    }
}
