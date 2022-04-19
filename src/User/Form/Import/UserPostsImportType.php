<?php

declare(strict_types=1);

namespace Future\Blog\User\Form\Import;

use Future\Blog\Post\Entity\Post;
use Future\Blog\User\Dto\Import\UserPostImportDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPostsImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'form.user_post_export.status',
                'choices' => Post::POST_IMPORT_STATUS,
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ])
            ->add('importFile', FileType::class, [
                'required' => true,
                'data_class' => null,
                'attr' => [
                    'placeholder' => 'form.user_post_export.import_file_placeholder',
                ],
            ])
            ->add('import', SubmitType::class, [
                'label' => 'form.user_post_import.button',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'data_class' => UserPostImportDto::class,
        ]);
    }
}
