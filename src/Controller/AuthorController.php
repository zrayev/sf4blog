<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorController extends AbstractController
{
    public function index()
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
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
}
