<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorController extends AbstractController
{
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $authors = $em->getRepository(Author::class)->findAll();
        $paginateAuthors = $paginator->paginate($authors, $request->query->getInt('page', 1), 10);

        return $this->render('author/index.html.twig', [
            'authors' => $paginateAuthors,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        $author = new Author();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($author);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your author with name - ' . $author->getName() . ' were saved!'
            );

            return $this->redirectToRoute('author_new');
        }

        return $this->render('author/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Author $author
     * @ParamConverter("author", class="App:Author")
     *
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, Author $author)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('authors');
        }

        return $this->render('author/edit.html.twig', [
            'form' => $form->createView(),
            'author' => $author,
        ]);
    }
}
