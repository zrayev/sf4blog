<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param $status
     *
     * @return Query
     */
    public function findAllPublishArticlesQuery($status): Query
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', $status)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
        ;
    }

    /**
     * @return Query
     */
    public function findAllArticlesQuery(): Query
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ;
    }

    /**
     * @param $category
     *
     * @return Query
     */
    public function findPostsForCategoryQuery(Category $category): Query
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :category')
            ->setParameter('category', $category)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ;
    }

    /**
     * @param $title
     *
     * @return Query
     */
    public function findByTitleQuery($title): Query
    {
        return $this->createQueryBuilder('p')
            ->where('p.title LIKE :title')
            ->setParameter(':title', "%$title%")
            ->getQuery()
            ;
    }
}
