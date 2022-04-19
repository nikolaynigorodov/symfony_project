<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller;

use Future\Blog\User\Entity\Import;
use Future\Blog\User\Repository\ImportReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserPostsImportReportController extends AbstractController
{
    private ImportReportRepository $importReportRepository;

    public function __construct(ImportReportRepository $importReportRepository)
    {
        $this->importReportRepository = $importReportRepository;
    }

    public function __invoke(Import $import, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        return $this->render('user/post/import/report.html.twig', [
            'reports' => $import->getImportReports(),
            'import' => $import,
        ]);
    }
}
