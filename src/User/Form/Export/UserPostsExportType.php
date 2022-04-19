<?php

declare(strict_types=1);

namespace Future\Blog\User\Form\Export;

use Future\Blog\Post\Entity\Category;
use Future\Blog\Post\Entity\Post;
use Future\Blog\User\Dto\Export\UserPostExportDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPostsExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'form.user_post_export.status',
                'choices' => Post::SEARCH_POST_STATUS,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'multiple' => true,
                'choice_label' => 'title',
                'label' => 'form.user_post_export.category',
                'required' => false,
            ])
            ->add('dateFrom', DateTimeType::class, [
                'label' => 'From',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('dateTo', DateTimeType::class, [
                'label' => 'To',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('export', SubmitType::class, [
                'label' => 'form.user_post_export.button',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'data_class' => UserPostExportDto::class,
        ]);
    }
}
