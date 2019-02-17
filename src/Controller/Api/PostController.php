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
     * Return List Comments.
     *
     * @FOSRest\Get(path="/comments/{post}", name="api_post_comments")
     * @SWG\Response(
     *     response=200,
     *     description="Success"
     * ),
     * @SWG\Tag(name="posts")
     * @Security(name="Bearer")
     * @param Post $post
     * @throws HttpException
     * @return Response
     * @ParamConverter("post", class="App:Post")
     */
    public function getComments(Post $post): Response
    {
        $comments = $post->getComments();
        if (!$comments) {
            throw new HttpException(400, 'Comments not found');
        }

        return $this->createApiResponse(['comments' => $comments]);
    }

    /**
     * Return List Tags.
     *
     * @FOSRest\Get(path="/tags/{post}", name="api_post_tags")
     * @SWG\Response(
     *     response=200,
     *     description="Success"
     * ),
     * @SWG\Tag(name="posts")
     * @Security(name="Bearer")
     * @param Post $post
     * @throws HttpException
     * @return Response
     * @ParamConverter("post", class="App:Post")
     */
    public function getTags(Post $post): Response
    {
        $tags = $post->getTags();
        if (!$tags) {
            throw new HttpException(400, 'Tags not found');
        }

        return $this->createApiResponse(['tags' => $tags]);
    }

    /**
     * Return Post Author.
     *
     * @FOSRest\Get(path="/author/{post}", name="api_post_author")
     * @SWG\Response(
     *     response=200,
     *     description="Success"
     * ),
     * @SWG\Tag(name="posts")
     * @Security(name="Bearer")
     * @param Post $post
     * @throws HttpException
     * @return Response
     * @ParamConverter("post", class="App:Post")
     */
    public function getAuthor(Post $post): Response
    {
        $author = $post->getAuthor();
        if (!$author) {
            throw new HttpException(400, 'Author not found');
        }

        return $this->createApiResponse(['author' => $author]);
    }

    /**
     * @param $data
     * @param int $statusCode
     *
     * @return Response
     */
    protected function createApiResponse($data, $statusCode = 200): Response
    {
        $json = $this->serialize($data);

        return new Response(
            $json, $statusCode, [
                'Content-Type' => 'application/json',
            ]
        );
    }

    /**
     * Use JMS Serializer to serialize objects.
     *
     * @param mixed $data
     * @param mixed $format
     *
     * @return string
     */
    protected function serialize($data, $format = 'json'): string
    {
        $serializer = SerializerBuilder::create()->build();

        return $serializer->serialize($data, $format);
    }
}
