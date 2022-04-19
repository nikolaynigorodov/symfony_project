<?php

declare(strict_types=1);

namespace Future\Blog\Post\Form;

use Future\Blog\Post\Dto\PostSearchDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'form.search.placeholder',
                ],
                'required' => true,
            ])
            ->add('Search', SubmitType::class, [
                'label' => 'form.search.button',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'data_class' => PostSearchDto::class,
        ]);
    }
}
