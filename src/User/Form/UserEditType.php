<?php

declare(strict_types=1);

namespace Future\Blog\User\Form;

use Future\Blog\User\Dto\UserUpdateDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'form.edit.profile.first_name',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'form.edit.profile.last_name',
            ])
            ->add('avatarFile', FileType::class, [
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'placeholder' => 'Select an avatar image',
                ],
            ])
            ->add('viewAvatar', HiddenType::class, [
                'required' => false,
            ])
            ->add('Registration', SubmitType::class, [
                'label' => 'form.edit.profile.submit_update',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'data_class' => UserUpdateDto::class,
        ]);
    }
}
