<?php

namespace App\Service;

use App\Pagination\PaginatedCollection;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * PaginationFactory constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param Query $qb
     * @param Request $request
     * @param $route
     * @param array $routeParams
     *
     * @return PaginatedCollection
     */
    public function createCollection(Query $qb, Request $request, $route, array $routeParams = []): PaginatedCollection
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per_page', 10);

        $adapter = new DoctrineORMAdapter($qb, true, false);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($perPage);
        $pagerfanta->setCurrentPage($page);

        $items = [];
        foreach ($pagerfanta->getCurrentPageResults() as $item) {
            $items[] = $item;
        }
        $paginatedCollection = new PaginatedCollection(
            $items,
            $pagerfanta->getNbResults()
        );

        $routeParams = array_merge($routeParams, $request->query->all());

        $createLinkUrl = function ($targetPage) use ($route, $routeParams) {
            return $this->router->generate($route, array_merge(
                $routeParams,
                ['page' => $targetPage]
            ));
        };

        $paginatedCollection->addLink('self', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl($pagerfanta->getNbPages()));

        if ($pagerfanta->hasNextPage()) {
            $paginatedCollection->addLink('next', $createLinkUrl($pagerfanta->getNextPage()));
        }

        if ($pagerfanta->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $createLinkUrl($pagerfanta->getPreviousPage()));
        }

        return $paginatedCollection;
    }
}
