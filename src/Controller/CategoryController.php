<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)->getCategories();

        if (!$categories) {
            return $this->render('category/index.html.twig', [
                'message' => 'Categories not found. You can will create new category.',
            ]);
        }

        $paginateCategories = $paginator->paginate($categories, $request->query->getInt('page', 1), 10);

        return $this->render('category/index.html.twig', [
            'categories' => $paginateCategories,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $em = $this->getDoctrine()->getManager();
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

    /**
     * @param Request $request
     * @param Category $category
     * @ParamConverter("category", class="App:Category")
     *
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CategoryType::class, $category);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->flush();

                return $this->redirectToRoute('categories');
            }
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }
}
