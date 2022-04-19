<?php

declare(strict_types=1);

namespace Future\Blog\User\Form;

use Future\Blog\User\Dto\UserRegistration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'form.registration.email',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'form.registration.password'],
                'second_options' => ['label' => 'form.registration.repeat_password'],
                'invalid_message' => 'The New Password and
                    New Password Repeat must match',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'form.registration.first_name',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'form.registration.last_name',
            ])
            ->add('Registration', SubmitType::class, [
                'label' => 'form.registration.submit_registration',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'data_class' => UserRegistration::class,
        ]);
    }
}
