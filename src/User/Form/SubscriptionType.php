<?php

declare(strict_types=1);

namespace Future\Blog\User\Form;

use Future\Blog\Post\Entity\Category;
use Future\Blog\User\Dto\SubscriptionDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'multiple' => true,
                'choice_label' => 'title',
                'label' => 'form.create.subscription_category',
            ])
            ->add('create', SubmitType::class, [
                'label' => 'form.create.subscription_button',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'data_class' => SubscriptionDto::class,
        ]);
    }
}
