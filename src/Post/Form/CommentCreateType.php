<?php

declare(strict_types=1);

namespace Future\Blog\Post\Form;

use Future\Blog\Post\Dto\CommentCreateDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextareaType::class, [
                'label' => 'form.comment.create.message_text',
                'required' => true,
            ])
            ->add('Search', SubmitType::class, [
                'label' => 'form.comment.create.button',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'data_class' => CommentCreateDto::class,
        ]);
    }
}
