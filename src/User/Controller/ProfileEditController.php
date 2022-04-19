<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Core\Entity\User;
use Future\Blog\Core\FileUploader\FileUploader;
use Future\Blog\User\Dto\UserUpdateDto;
use Future\Blog\User\Form\UserEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileEditController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private TranslatorInterface $translator;

    private FileUploader $fileUploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        FileUploader $fileUploader
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->fileUploader = $fileUploader;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Trying to sign in without an account.');
        $user = $this->getUser();

        $userDto = $this->setUserDto($user);
        $form = $this->createForm(UserEditType::class, $userDto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updateUser($user, $userDto);
            $this->addFlash('success', $this->translator->trans('profile.update.success'));

            return $this->redirectToRoute('user_user_information');
        }

        return $this->render('user/profile_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function setUserDto(User $user): UserUpdateDto
    {
        $userEdit = new UserUpdateDto();
        $userEdit->setFirstName($user->getFirstName());
        $userEdit->setLastName($user->getLastName());
        if ($user->getAvatar()) {
            $userEdit->setViewAvatar($user->getAvatar());
        }

        return $userEdit;
    }

    private function updateUser(User $user, UserUpdateDto $userDto): void
    {
        $user->setFirstName($userDto->getFirstName());
        $user->setLastName($userDto->getLastName());
        $oldAvatar = $user->getAvatar();
        $avatarObjectUploadedFile = $userDto->getAvatarFile();
        if ($avatarObjectUploadedFile) {
            $avatarFileName = $this->fileUploader->upload($avatarObjectUploadedFile);
            $user->setAvatar($avatarFileName);
            if ($oldAvatar) { // Delete Old Avatar
                $this->fileUploader->removeImages($oldAvatar);
            }
        }
        $this->entityManager->flush();
    }
}
