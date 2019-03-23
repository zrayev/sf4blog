<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class Pagination
{
    private $knpPaginator;
    private $em;

    public function __construct(EntityManagerInterface $em, PaginatorInterface $knpPaginator)
    {
        $this->em = $em;
        $this->knpPaginator = $knpPaginator;
    }

    /**
     * @param Request $request
     * @param $count
     *
     * @return PaginationInterface
     */
    public function paginationBlogIndexQuery(Request $request, $count): PaginationInterface
    {
        $query = $this->em->getRepository(Post::class)->findAllPublishPostsQuery();
        $pagination = $this->knpPaginator->paginate(
            $query,
            $request->query->getInt('page', 1), $count
        );

        return $pagination;
    }

    /**
     * @param Request $request
     * @param Category $category
     * @param $count
     *
     * @return PaginationInterface
     */
    public function paginationPostsForCategoryQuery(Request $request, Category $category, $count): PaginationInterface
    {
        $query = $this->em->getRepository(Post::class)->findPostsForCategoryQuery($category);
        $pagination = $this->knpPaginator->paginate(
            $query,
            $request->query->getInt('page', 1), $count
        );

        return $pagination;
    }
}
