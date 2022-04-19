<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller\Import;

use Future\Blog\User\Repository\ImportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserPostsImportAllReportsController extends AbstractController
{
    private ImportRepository $importRepository;

    public function __construct(ImportRepository $importRepository)
    {
        $this->importRepository = $importRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $user = $this->getUser();
        $importReports = $this->importRepository->findByUser($user);

        return $this->render('user/post/import/import_view_report.html.twig', [
            'importReports' => $importReports,
        ]);
    }
}
