<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryController extends Controller
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'index');
        $breadcrumbs->addItem('Categories', $this->get('router')->generate('categories'));
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)->findAll();
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
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('index'));
        $breadcrumbs->addItem('Categories', $this->get('router')->generate('categories'));
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $category = new Category();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash(
                'notice',
                $this->translator->trans('notification.category_created', [
                    '%title%' => $category->getTitle(),
                ])
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
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('index'));
        $breadcrumbs->addItem('Categories', $this->get('router')->generate('categories'));
        $breadcrumbs->addItem($category->getTitle());
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash(
                'notice',
                $this->translator->trans('notification.category_edited', [
                    '%title%' => $category->getTitle(),
                ])
            );

            return $this->redirectToRoute('categories');
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }
}
