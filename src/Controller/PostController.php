<?php

namespace App\Controller;

use App\Entity\Post;
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

        if (!$posts) {
            return $this->render('post/index.html.twig', [
                'message' => 'Articles not found. You can will create new article.',
            ]);
        }

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

            return $this->redirectToRoute('blog_new');
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
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PostType::class, $post);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->flush();

                return $this->redirectToRoute('blog');
            }
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }
}
