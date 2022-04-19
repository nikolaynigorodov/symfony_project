<?php

declare(strict_types=1);

namespace Future\Blog\Admin\Form;

use Future\Blog\Admin\Dto\ContactReplyMessageDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactReplyMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextareaType::class, [
                'label' => 'form.admin.reply_contact.message',
                'attr' => [
                    // 'class' => 'tinymce',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.admin.reply_contact.submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'data_class' => ContactReplyMessageDto::class,
        ]);
    }
}
