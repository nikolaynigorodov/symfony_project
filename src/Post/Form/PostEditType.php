<?php

declare(strict_types=1);

namespace Future\Blog\Post\Form;

use Future\Blog\Post\Dto\PostDto;
use Future\Blog\Post\Entity\Category;
use Future\Blog\Post\Form\DataTransformer\TagsTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostEditType extends AbstractType
{
    private TagsTransformer $transformer;

    public function __construct(TagsTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'form.create_post.title',
            ])
            ->add('summary', TextareaType::class, [
                'label' => 'form.create_post.summary',
                'required' => $options['valid'],
                'empty_data' => '',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'form.create_post.content',
                'required' => $options['valid'],
                'attr' => [
                    'class' => 'tinymce',
                ],
                'empty_data' => '',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'placeholder' => '',
                'required' => $options['valid'],
                'choice_label' => 'title',
                'empty_data' => '',
            ])
            ->add('tags', TextType::class, [
                'label' => 'form.create_post.tags',
                'required' => false,
                'invalid_message' => 'post.edit.tags_message',
                'empty_data' => '',
            ])
            ->add('status', TextType::class, [
                'label' => 'form.create_post.status',
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('publishingDate', DateTimeType::class, [
                'label' => 'Time to Delayed Post',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'placeholder' => 'post.edit.image.placeholder',
                ],
            ])
            ->add('viewImage', HiddenType::class, [
                'required' => false,
            ])
            ->add('update', SubmitType::class, [
                'label' => 'form.edit.update',
            ])
            ->add('saveDraft', SubmitType::class, [
                'label' => 'form.create_post.draft',
                'attr' => ['class' => 'btn btn-warning'],
            ])
            ->add('saveArchive', SubmitType::class, [
                'label' => 'form.create_post.archive',
                'attr' => ['class' => 'btn btn-info'],
            ])
        ;
        $builder->get('tags')
            ->addModelTransformer($this->transformer)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'valid' => false,
            // 'data_class' => PostDto::class,
        ]);
    }
}
