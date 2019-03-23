<?php

namespace App\Controller;

use App\Entity\Category;
use App\Service\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class CategoryController extends AbstractController
{
    private $translator;
    private $pagination;

    public function __construct(TranslatorInterface $translator, Pagination $pagination)
    {
        $this->translator = $translator;
        $this->pagination = $pagination;
    }

    /**
     * @param Request $request
     * @param $count
     * @param Category $category
     * @param Breadcrumbs $breadcrumbs
     *
     * @return Response
     * @ParamConverter("category", options={"mapping" : {"categorySlug" : "slug"}})
     */
    public function getCategoryPosts(Request $request, $count, Category $category, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem('Home', $this->get('router')->generate('index'));
        $breadcrumbs->addItem($category->getTitle());
        $paginatedPosts = $this->pagination->paginationPostsForCategoryQuery($request, $category, $count);

        return $this->render('post/index.html.twig', [
            'posts' => $paginatedPosts,
        ]);
    }
}
