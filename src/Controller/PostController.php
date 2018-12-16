<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    public function index()
    {
        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findAll()
        ;

        if (!$posts) {
            return $this->render('post/index.html.twig', [
                'message' => 'Articles not found. You can will create new article.',
            ]);
        }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($post);
            $em->flush();

            return new Response('Saved new post with id ' . $post->getId());
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $slug
     *
     * @return Response
     */
    public function show($slug): Response
    {
        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findOneBy(['slug' => $slug])
        ;

        if (!$post) {
            throw $this->createNotFoundException(
                'No article found for slug: ' . $slug
            );
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    public function edit($slug, Request $request)
    {
        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findOneBy(['slug' => $slug])
        ;

        if (!$post) {
            throw $this->createNotFoundException(
                'No article found for slug: ' . $slug
            );
        }

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PostType::class, $post);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->flush();

                return $this->redirectToRoute('posts_refactor');
            }
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }
}
