<?php

namespace App\Controller\Rest;

use App\Entity\Post;
use App\Service\PaginationFactory;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use HttpException;
use JMS\Serializer\SerializerBuilder;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractFOSRestController
{
    private $em;
    private $paginationFactory;

    /**
     * PostController constructor.
     * @param EntityManagerInterface $em
     * @param PaginationFactory $paginationFactory
     */
    public function __construct(EntityManagerInterface $em, PaginationFactory $paginationFactory)
    {
        $this->em = $em;
        $this->paginationFactory = $paginationFactory;
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
     * @FOSRest\Get(path="/posts", name="api_posts_collection")
     * @param Request $request
     * @return Response
     */
    public function getPosts(Request $request): Response
    {
        $qb = $this->em->getRepository(Post::class)->findAllQuery();

        $paginatedCollection = $this->paginationFactory->createCollection($qb, $request, 'api_posts_collection');

        return $this->createApiResponse($paginatedCollection);
    }

    /**
     * Centralise Response creation for our controllers.
     *
     * @param $data
     * @param int $statusCode
     *
     * @return Response
     */
    protected function createApiResponse($data, $statusCode = 200)
    {
        $json = $this->serialize($data);

        return new Response(
            $json, $statusCode, [
                'Content-Type' => 'application/json',
            ]
        );
    }

    /**
     * Use JMS Serialiser to serialize objects.
     *
     * @param mixed $data
     * @param mixed $format
     *
     * @return string
     */
    protected function serialize($data, $format = 'json')
    {
        $serializer = SerializerBuilder::create()->build();

        return $serializer->serialize($data, $format);
    }
}
