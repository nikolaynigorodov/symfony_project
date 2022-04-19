<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller\Import;

use Future\Blog\Core\FileUploader\FileUploader;
use Future\Blog\User\Dto\Import\UserPostImportDto;
use Future\Blog\User\Form\Import\UserPostsImportType;
use Future\Blog\User\Mapper\UserPostsImportMapper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserPostsImportController extends AbstractController
{
    private MessageBusInterface $bus;

    private TranslatorInterface $translator;

    private UserPostsImportMapper $importMapper;

    private FileUploader $fileUploader;

    public function __construct(
        MessageBusInterface $bus,
        TranslatorInterface $translator,
        UserPostsImportMapper $importMapper,
        FileUploader $fileUploader
    ) {
        $this->bus = $bus;
        $this->translator = $translator;
        $this->importMapper = $importMapper;
        $this->fileUploader = $fileUploader;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $userPostImportDto = new UserPostImportDto();
        $form = $this->createForm(UserPostsImportType::class, $userPostImportDto);
        $form->handleRequest($request);
        $uniqueFileNameAndToken = uniqid();
        if ($form->isSubmitted() && $form->isValid()) {
            $postImport = $this->importMapper->setUserPostImportDto($userPostImportDto, $this->getUser(), $uniqueFileNameAndToken);
            $this->bus->dispatch($postImport);
            $this->addFlash('success', $this->translator->trans('confirm.user.posts.import'));

            return $this->redirectToRoute('user_user_information');
        }

        return $this->render('user/post/import/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
