<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    public function index()
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
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
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your category with title - ' . $category->getTitle() . ' were saved!'
            );

            return $this->redirectToRoute('category_new');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
