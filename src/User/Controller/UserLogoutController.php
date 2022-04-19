<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserLogoutController extends AbstractController
{
    /**
     * @Route("/logout", name="app_logout")
     */
    public function __invoke(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
