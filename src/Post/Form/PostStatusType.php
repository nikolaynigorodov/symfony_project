<?php

declare(strict_types=1);

namespace Future\Blog\Post\Form;

use Future\Blog\Post\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostStatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'form.search_post_status.status_title',
                'choices' => Post::SEARCH_POST_STATUS,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('Search', SubmitType::class, [
                'label' => 'form.search_post_status.submit_name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
        ]);
    }
}
