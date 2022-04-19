<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller\Export;

use Future\Blog\User\Dto\Export\UserPostExportDto;
use Future\Blog\User\Form\Export\UserPostsExportType;
use Future\Blog\User\Mapper\UserPostsExportMapper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserPostsExportController extends AbstractController
{
    private UserPostsExportMapper $exportMapper;

    private MessageBusInterface $bus;

    private TranslatorInterface $translator;

    public function __construct(
        UserPostsExportMapper $exportMapper,
        MessageBusInterface $bus,
        TranslatorInterface $translator
    ) {
        $this->exportMapper = $exportMapper;
        $this->bus = $bus;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $userPostExportDto = new UserPostExportDto();
        $form = $this->createForm(UserPostsExportType::class, $userPostExportDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $postExport = $this->exportMapper->setUserPostExportDto($userPostExportDto, $this->getUser());
            $this->bus->dispatch($postExport);
            $this->addFlash('success', $this->translator->trans('confirm.user.posts.export'));

            return $this->redirectToRoute('user_user_information');
        }

        return $this->render('user/post/export/export.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
