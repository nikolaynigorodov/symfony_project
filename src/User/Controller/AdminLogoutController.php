<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminLogoutController extends AbstractController
{
    /**
     * @Route("/admin/logout", name="app_admin_logout")
     */
    public function __invoke(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
