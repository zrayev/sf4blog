<?php

namespace App\Menu;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder
{
    private $factory;
    private $em;
    private $authorizationChecker;

    /**
     * @param FactoryInterface $factory
     * @param EntityManagerInterface $em
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(FactoryInterface $factory, EntityManagerInterface $em, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->factory = $factory;
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param RequestStack $requestStack
     *
     * @return ItemInterface
     */
    public function MainMenu(RequestStack $requestStack)
    {
        $masterCategories = $this->em->getRepository(Category::class)
            ->findBy(['parent' => null]);
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav mr-auto');

        foreach ($masterCategories as $masterCategory) {
            $params = ['categorySlug' => $masterCategory->getSlug()];
            $menu->addChild($masterCategory->getTitle(), ['route' => 'category_posts', 'routeParameters' => $params]);
        }

        $menu->addChild('Posts', ['route' => 'posts']);
        $menu->addChild('Categories', ['route' => 'categories']);
        $menu->addChild('Tags', ['route' => 'tags']);

        foreach ($menu as $child) {
            $child->setLinkAttribute('class', 'nav-link')
                ->setAttribute('class', 'nav-item');
        }

        return $menu;
    }

    public function UserMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(['class' => 'navbar-nav mr-auto navbar-right']);
        if ($this->authorizationChecker->isGranted(['ROLE_ADMIN'])) {
            $menu->addChild('Admin', ['route' => 'sonata_admin_redirect', 'label' => 'Admin']);
        }
        if ($this->authorizationChecker->isGranted(['ROLE_USER'])) {
            $menu->addChild('Logout', ['route' => 'logout', 'label' => 'Logout']);
        } else {
            $menu->addChild('Login', ['route' => 'app_login', 'label' => 'Login']);
        }

        foreach ($menu as $child) {
            $child->setLinkAttribute('class', 'nav-link')
                ->setAttribute('class', 'nav-item');
        }

        return $menu;
    }
}
