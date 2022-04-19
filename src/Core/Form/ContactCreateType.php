<?php

declare(strict_types=1);

namespace Future\Blog\Core\Form;

use Future\Blog\Core\Dto\ContactDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.contact.name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.contact.email',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'form.contact.message',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.contact.submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'data_class' => ContactDto::class,
        ]);
    }
}
