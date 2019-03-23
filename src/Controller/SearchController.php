<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\SearchType;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class SearchController extends AbstractController
{
    private $knpPaginator;

    /**
     * SearchController constructor.
     * @param PaginatorInterface $knpPaginator
     */
    public function __construct(PaginatorInterface $knpPaginator)
    {
        $this->knpPaginator = $knpPaginator;
    }

    /**
     * @param Request $request
     * @param RepositoryManagerInterface $repositoryManager
     * @param Breadcrumbs $breadcrumbs
     *
     * @return Response
     */
    public function index(Request $request, RepositoryManagerInterface $repositoryManager, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem('Home', $this->get('router')->generate('index'));
        $breadcrumbs->addRouteItem('Search', 'search');
        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);
        $searchData = $searchForm->getData();
        $query = $searchData->getQuery();

        if ($query !== null) {
            $posts = $repositoryManager
                ->getRepository(Post::class)
                ->find($query)
            ;
            $paginatedPosts = $this->knpPaginator->paginate(
                $posts,
                $request->query->getInt('page', 1), 5
            );
        }

        /** @var $paginatedPosts */
        return $this->render('post/search.html.twig', [
            'query' => $query,
            'posts' => $paginatedPosts,
        ]);
    }

    /**
     * @return Response
     */
    public function renderSearchForm(): Response
    {
        $searchForm = $this->createForm(SearchType::class);

        return $this->render('layouts/_search-form.html.twig', [
            'form' => $searchForm->createView(),
        ]);
    }
}
