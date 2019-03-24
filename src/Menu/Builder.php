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
    public function MainMenu(RequestStack $requestStack): ItemInterface
    {
        $masterCategories = $this->em->getRepository(Category::class)
            ->findBy(['parent' => null]);
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav mr-auto');

        foreach ($masterCategories as $masterCategory) {
            $params = ['categorySlug' => $masterCategory->getSlug()];
            $menu->addChild($masterCategory->getTitle(), ['route' => 'category_posts', 'routeParameters' => $params]);
        }

        foreach ($menu as $child) {
            $child->setLinkAttribute('class', 'nav-link')
                ->setAttribute('class', 'nav-item');
        }

        return $menu;
    }

    /**
     * @param RequestStack $requestStack
     *
     * @return ItemInterface
     */
    public function UserMenu(RequestStack $requestStack): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(['class' => 'navbar-nav mr-auto navbar-right']);
        if ($this->authorizationChecker->isGranted(['ROLE_ADMIN'])) {
            $menu->addChild('Admin', ['route' => 'sonata_admin_redirect', 'label' => 'Admin']);
        }
        if ($this->authorizationChecker->isGranted(['``'])) {
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
