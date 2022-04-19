<?php

declare(strict_types=1);

namespace Future\Blog\Core\Menu;

use Future\Blog\Post\Repository\CategoryRepository;
use Future\Blog\Stripe\Manager\UserPostCreateChecker;
use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class MenuBuilder
{
    private FactoryInterface $factory;

    private Security $security;

    private CategoryRepository $categoryRepository;

    private TokenStorageInterface $tokenStorage;

    private UserPostCreateChecker $postCreateChecker;

    public function __construct(
        FactoryInterface $factory,
        Security $security,
        CategoryRepository $categoryRepository,
        TokenStorageInterface $tokenStorage,
        UserPostCreateChecker $postCreateChecker
    ) {
        $this->factory = $factory;
        $this->security = $security;
        $this->categoryRepository = $categoryRepository;
        $this->tokenStorage = $tokenStorage;
        $this->postCreateChecker = $postCreateChecker;
    }

    public function createMainMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'navbar-nav mr-auto',
            ],
        ]);
        $menu->addChild('menu.posts', ['route' => 'post_post_all_show']);
        $query = $this->categoryRepository->findAll();
        if ($query) {
            $dropdownCatagories = $menu->addChild(
                'menu.categories_title',
                [
                    'attributes' => [
                        'dropdown' => true,
                    ],
                ]
            );
            foreach ($query as $category) {
                $dropdownCatagories->addChild($category->getTitle(), [
                    'route' => 'post_category_show',
                    'routeParameters' => ['id' => $category->getId(),
                    ],
                ]);
            }
        }

        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            if ($this->security->isGranted('post_link_create')) {
                $menu->addChild('menu.post.create', ['route' => 'post_post_create']);
            }
            $dropdown = $menu->addChild(
                'menu.profile',
                [
                    'attributes' => [
                        'dropdown' => true,
                    ],
                ]
            );
            $dropdown->addChild('menu.profile', ['route' => 'user_user_information']);
            $dropdown->addChild('menu.posts_my', ['route' => 'user_post_user_posts']);
            $dropdown->addChild('menu.profile_edit', ['route' => 'user_user_profile_edit']);
            $menu->addChild('menu.subscription_pay', ['route' => 'stripe_start']);
            $menu->addChild('menu.logout', ['route' => 'app_logout']);
        } else {
            $menu->addChild('menu.login', ['route' => 'app_login']);
            $menu->addChild('menu.registration', ['route' => 'user_user_registration']);
        }

        $menu->addChild('menu.contact', ['route' => 'core_contact_create']);

        return $menu;
    }
}
