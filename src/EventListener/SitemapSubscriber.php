<?php

namespace App\EventListener;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param EntityManagerInterface $em
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, EntityManagerInterface $em)
    {
        $this->urlGenerator = $urlGenerator;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'registerPostsPages',
        ];
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function registerPostsPages(SitemapPopulateEvent $event)
    {
        $posts = $this->em->getRepository(Post::class)->findAllPublishPostsQuery()->getResult();
        $masterCategories = $this->em->getRepository(Category::class)->findBy(['parent' => null]);
        foreach ($posts as $post) {
            /** @var Post $post */
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'post_show',
                        [
                            'slug' => $post->getSlug(),
                            '_locale' => 'en',
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ),
                'posts'
            );
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'post_show',
                        [
                            'slug' => $post->getSlug(),
                            '_locale' => 'uk',
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ),
                'posts'
            );
        }
        foreach ($masterCategories as $category) {
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'category_posts',
                        [
                            'categorySlug' => $category->getSlug(),
                            '_locale' => 'en',
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ),
                'category_posts'
            );
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'category_posts',
                        [
                            'categorySlug' => $category->getSlug(),
                            '_locale' => 'uk',
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ),
                'category_posts'
            );
        }
    }
}
