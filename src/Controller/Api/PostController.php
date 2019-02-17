<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Service\PaginationFactory;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use HttpException;
use JMS\Serializer\SerializerBuilder;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     *     @Model(type=Post::class, groups={"post:show"})
     * )
     * @SWG\Tag(name="posts")
     * @Security(name="Bearer")
     *
     * @FOSRest\Get("/posts/{id<\d+>}")
     * @param Post $post
     * @throws HttpException
     * @return response
     * @ParamConverter("post", class="App:Post")
     */
    public function getPost(Post $post): Response
    {
        if (!$post) {
            throw new HttpException(400, 'Post not found');
        }

        return $this->createApiResponse(['post' => $post]);
    }

    /**
     * Return list of paginatedCollection posts.
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return list of paginatedCollection posts",
     *     @Model(type=Post::class, groups={"post:show"})
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="per_page",
     *     in="query",
     *     description="Page items count",
     *     type="integer",
     * ),
     * @SWG\Tag(name="posts")
     * @Security(name="Bearer")
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
