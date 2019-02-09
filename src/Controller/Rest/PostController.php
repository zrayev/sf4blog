<?php

namespace App\Controller\Rest;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use HttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PostController extends AbstractFOSRestController
{
    private $em;

    /**
     * PostController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @FOSRest\Get("/post/{id}")
     * @param mixed $id
     */
    public function getPost($id)
    {
        if (!$id) {
            throw new HttpException(400, 'Invalid id');
        }
        $post = $this->em->getRepository(Post::class)->find($id);

        if (!$post) {
            throw new HttpException(400, 'Invalid data');
        }

        return $this->createApiResponse(['post' => $post]);
    }

    /**
     * @FOSRest\Get("/posts")
     */
    public function getPosts()
    {
        $posts = $this->em->getRepository(Post::class)->findAll();

        return $this->createApiResponse($posts);
    }

    /**
     * @param $data
     *
     * @return Response
     */
    protected function createApiResponse($data): Response
    {
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [$encoder]);
        $jsonData = $serializer->serialize($data, 'json');

        return new JsonResponse(
            $jsonData,
            200,
            [],
            true
        );
    }
}
