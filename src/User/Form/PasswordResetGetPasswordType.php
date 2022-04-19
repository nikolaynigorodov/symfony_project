<?php

declare(strict_types=1);

namespace Future\Blog\User\Form;

use Future\Blog\User\Dto\PasswordResetGetPasswordDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordResetGetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'form.registration.password'],
                'second_options' => ['label' => 'form.registration.repeat_password'],
                'invalid_message' => 'The New Password and
                    New Password Repeat must match',
            ])
            ->add('Send', SubmitType::class, [
                'label' => 'form.password.reset_send',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'data_class' => PasswordResetGetPasswordDto::class,
        ]);
    }
}
