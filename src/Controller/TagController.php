<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TagController extends AbstractController
{
    public function index()
    {
        return $this->render('tag/index.html.twig', [
            'controller_name' => 'TagController',
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
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your tag  with title - ' . $tag->getTitle() . ' were saved!'
            );

            return $this->redirectToRoute('tag_new');
        }

        return $this->render('tag/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
