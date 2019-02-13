<?php

namespace App\Controller\Rest;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use HttpException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class PostController extends AbstractFOSRestController
{
    private $em;
    private $serializer;

    /**
     * PostController constructor.
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    /**
     * Return post by ID.
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return Post by ID",
     *     @Model(type=Post::class, groups={"rest"})
     * )
     * @SWG\Tag(name="posts")
     * @Security(name="Post")
     *
     * @FOSRest\Get("/post/{id}")
     * @param mixed $id
     * @throws \HttpException
     * @return response
     */
    public function getPost($id): Response
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
     * Return all posts.
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return all posts",
     *     @Model(type=Post::class, groups={"rest"})
     * )
     * @SWG\Tag(name="posts")
     * @Security(name="Post")
     *
     * @FOSRest\Get("/posts")
     */
    public function getPosts(): Response
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
        $jsonData = $this->serializer->serialize(
            ['data' => $data], 'json', ['groups' => ['rest'],
        ]);

        return new JsonResponse(
            $jsonData,
            200,
            [],
            true
        );
    }
}
