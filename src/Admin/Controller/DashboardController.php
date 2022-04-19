<?php

declare(strict_types=1);

namespace Future\Blog\Admin\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Future\Blog\Core\Entity\Contact;
use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Entity\Category;
use Future\Blog\Post\Entity\Comment;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Entity\Tag;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Future Blog Loc')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('User');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Contact', 'fa fa-share', Contact::class);
        yield MenuItem::section('Content');
        yield MenuItem::linkToCrud('Posts', 'fa fa-newspaper-o', Post::class);
        yield MenuItem::linkToCrud('Category', 'fa fa-comments', Category::class);
        yield MenuItem::linkToCrud('Tags', 'fa fa-tags', Tag::class);
        yield MenuItem::linkToCrud('Post Comments', 'fa fa-feather', Comment::class);
    }
}
