<?php

declare(strict_types=1);

namespace Future\Blog\Admin\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Future\Blog\Core\Entity\Contact;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email'),
            TextField::new('name'),
            TextField::new('message'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $replyMessageLink = Action::new('Reply Message', 'Reply Message', 'fa fa-paper-plane')
            ->linkToRoute('admin_contact_reply_message', function (Contact $contact): array {
                return [
                    'id' => $contact->getId(),
                ];
            })
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $replyMessageLink)

            ->remove(Crud::PAGE_INDEX, Action::NEW)

            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ;
    }
}
