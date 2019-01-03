<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $status = Post::STATUS_PUBLISH;
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findAllPublishArticles($status);
        $blogPosts = $paginator->paginate($posts, $request->query->getInt('page', 1), 9);

        return $this->render('post/index.html.twig', [
            'posts' => $blogPosts,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $post = new Post();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($post);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your post  with title - ' . $post->getTitle() . ' were saved!'
            );

            return $this->redirectToRoute('blog');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Post $post
     * @ParamConverter("post", class="App:Post")
     *
     * @return Response
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @param Request $request
     * @param Post $post
     * @ParamConverter("post", class="App:Post")
     *
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, Post $post)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($post->getAuthor() !== $this->getUser()) {
            $this->addFlash(
                'notice',
                'You don\'t have permission for this operation!'
            );

            return $this->redirectToRoute('blog');
        }
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('blog');
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    /**
     * @param Request $request
     * @ParamConverter("post", options={"mapping" : {"postSlug" : "slug"}})
     * @param Post $post
     * @return Response
     */
    public function commentNew(Request $request, Post $post): Response
    {
        $comment = new Comment();
        $post->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your comment with title - ' . $comment->getTitle() . ' were saved!'
            );

            return $this->redirectToRoute('post_show', [
                'slug' => $post->getSlug(), ]);
        }

        return $this->render('comment/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    public function commentForm(Post $post): Response
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('comment/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Post $post
     * @ParamConverter("post", class="App:Post")
     *
     * @return Response
     */
    public function delete(Post $post): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($post->getAuthor() !== $this->getUser()) {
            $this->addFlash(
                'notice',
                'You don\'t have permission for this operation!'
            );

            return $this->redirectToRoute('blog');
        }
        $em = $this->getDoctrine()->getManager();
        $post->getTags()->clear();
        $post->getComments()->clear();
        $em->remove($post);
        $em->flush();
        $this->addFlash(
            'notice',
            'Your post deleted!'
        );

        return $this->redirectToRoute('blog');
    }
}
