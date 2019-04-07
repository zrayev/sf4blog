<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Service\PaginationFactory;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Exception\NotFoundException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class PostController extends AbstractFOSRestController
{
    private $em;
    private $paginationFactory;
    private $serializer;

    /**
     * PostController constructor.
     * @param EntityManagerInterface $em
     * @param PaginationFactory $paginationFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $em, PaginationFactory $paginationFactory, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->paginationFactory = $paginationFactory;
        $this->serializer = $serializer;
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
     * @return response
     * @ParamConverter("post", class="App:Post")
     */
    public function getPost(Post $post): Response
    {
        return $this->createApiResponse(['data' => $post], ['groups' => 'post:show']);
    }

    /**
     * Delete post by ID.
     *
     * @SWG\Response(
     *     response=204,
     *     description="Delete Post by ID",
     * )
     * @SWG\Tag(name="posts")
     * @Security(name="Bearer")
     *
     * @FOSRest\Delete("/posts/{id<\d+>}")
     * @param Post $post
     * @ParamConverter("post", class="App:Post")
     * @return Response
     */
    public function deletePost(Post $post): Response
    {
        $post->getTags()->clear();
        $post->getComments()->clear();
        $this->em->remove($post);
        $this->em->flush();

        return new Response('', Response::HTTP_NO_CONTENT, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Return list of paginatedCollection posts.
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return list of paginatedCollection posts",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="items", type="array", @SWG\Items(ref=@Model(type=Post::class, groups={"post:show"}))),
     *         @SWG\Property(property="total", type="integer"),
     *         @SWG\Property(property="count", type="integer"),
     *     )
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

        return $this->createApiResponse($paginatedCollection, ['groups' => ['paginatedCollection:show', 'post:show']]);
    }

    /**
     * New post.
     *
     * @SWG\Parameter(
     *     name="Post",
     *     in="body",
     *     description="Create new Post",
     *     type="object",
     *     @Model(type=Post::class, groups={"post:edit"})
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Create Post",
     *     @Model(type=Post::class, groups={"post:edit"})
     * )
     * @SWG\Tag(name="posts")
     * @Security(name="Bearer")
     *
     * @FOSRest\Post("/posts")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        /** @var Post $post */
        $post = $this->serializer->deserialize($request->getContent(), Post::class, 'json', ['groups' => 'post:edit']);
        $post->setAuthor($this->getUser());
        $this->em->persist($post);
        $this->em->flush();

        return new Response($post);
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
     * @throws NotFoundException
     * @return Response
     * @ParamConverter("post", class="App:Post")
     */
    public function getComments(Post $post): Response
    {
        $comments = $post->getComments();
        if (!$comments) {
            throw new NotFoundException(404, 'Comments not found');
        }

        return $this->createApiResponse(['data' => $comments], ['groups' => 'comment:show']);
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
     * @throws NotFoundException
     * @return Response
     * @ParamConverter("post", class="App:Post")
     */
    public function getTags(Post $post): Response
    {
        $tags = $post->getTags();
        if (!$tags) {
            throw new NotFoundException(404, 'Tags not found');
        }

        return $this->createApiResponse(['data' => $tags], ['groups' => 'tag:show']);
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
     * @throws NotFoundException
     * @return Response
     * @ParamConverter("post", class="App:Post")
     */
    public function getAuthor(Post $post): Response
    {
        $author = $post->getAuthor();
        if (!$author) {
            throw new NotFoundException(404, 'Author not found');
        }

        return $this->createApiResponse(['data' => $author], ['groups' => 'author:show']);
    }

    /**
     * @param $data
     * @param $context
     * @param int $statusCode
     *
     * @return Response
     */
    protected function createApiResponse($data, array $context = [], $statusCode = 200): Response
    {
        $json = $this->serialize($data, $context);

        return new Response(
            $json, $statusCode, [
                'Content-Type' => 'application/json',
            ]
        );
    }

    /**
     * @param mixed $data
     * @param $context
     * @param mixed $format
     *
     * @return string
     */
    protected function serialize($data, $context, $format = 'json'): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }
}
