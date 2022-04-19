<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserInformationController extends AbstractController
{
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('user/user_information.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
