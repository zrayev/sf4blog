<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
     * @return mixed
     */
    public function findAllPublishArticles($status)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :val')
            ->setParameter('val', $status)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
        ;
    }

    /**
     * @param $category
     *
     * @return Query
     */
    public function findPostsForCategory(Category $category): Query
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :val')
            ->setParameter('val', $category->getId())
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ;
    }

    /**
     * @param Category $category
     * @throws NonUniqueResultException
     * @return mixed
     */
    public function getCategoryPostsCount(Category $category)
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.category = :val')
            ->setParameter('val', $category->getId())
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
}
