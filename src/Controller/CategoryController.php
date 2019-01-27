<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\CategoryType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class CategoryController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Breadcrumbs $breadcrumbs
     *
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addRouteItem('Home', 'index');
        $breadcrumbs->addItem('Categories', $this->get('router')->generate('categories'));
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)->findAllQuery();
        $paginateCategories = $paginator->paginate($categories, $request->query->getInt('page', 1), 10);

        return $this->render('category/index.html.twig', [
            'categories' => $paginateCategories,
        ]);
    }

    /**
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     *
     * @return Response
     */
    public function new(Request $request, Breadcrumbs $breadcrumbs): Response
    {
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
     * @param Breadcrumbs $breadcrumbs
     *
     * @return RedirectResponse|Response
     * @ParamConverter("category", class="App:Category")
     */
    public function edit(Request $request, Category $category, Breadcrumbs $breadcrumbs)
    {
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

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Category $category
     * @param Breadcrumbs $breadcrumbs
     *
     * @return Response
     * @ParamConverter("category", options={"mapping" : {"categorySlug" : "slug"}})
     */
    public function getCategoryPosts(Request $request, PaginatorInterface $paginator, Category $category, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem('Home', $this->get('router')->generate('index'));
        $breadcrumbs->addItem($category->getTitle());

        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findPostsForCategoryQuery($category);
        $paginatedPosts = $paginator->paginate($posts, $request->query->getInt('page', 1), 9);

        return $this->render('post/index.html.twig', [
            'posts' => $paginatedPosts,
        ]);
    }
}
