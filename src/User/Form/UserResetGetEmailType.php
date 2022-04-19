<?php

declare(strict_types=1);

namespace Future\Blog\User\Form;

use Future\Blog\User\Dto\UserEmailResetGetEmailDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserResetGetEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'form.password.reset_email',
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
            'data_class' => UserEmailResetGetEmailDto::class,
        ]);
    }
}
