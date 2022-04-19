<?php

declare(strict_types=1);

namespace Future\Blog\Admin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Future\Blog\Post\Entity\Comment;
use Future\Blog\Post\Repository\CommentRepository;

class PostCommentsCrudController extends AbstractCrudController
{
    private CommentRepository $commentRepository;

    private EntityManagerInterface $em;

    public function __construct(CommentRepository $commentRepository, EntityManagerInterface $em)
    {
        $this->commentRepository = $commentRepository;
        $this->em = $em;
    }

    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id'),
            AssociationField::new('author'),
            TextField::new('message'),
            AssociationField::new('post'),
            DateTimeField::new('createdAt'),
            IntegerField::new('getStatusAdmin'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $approved = Action::new('approved', 'Approved', 'btn btn-success')
            ->linkToCrudAction('CommentChangeStatusApproved')
        ;

        $declined = Action::new('declined', 'Declined', 'btn btn-danger')
            ->linkToCrudAction('CommentChangeStatusDeclined')
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $approved)
            ->add(Crud::PAGE_INDEX, $declined)

            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)

            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ;
    }

    public function CommentChangeStatusApproved(AdminContext $context)
    {
        $id = $context->getEntity()->getPrimaryKeyValue();
        $comment = $this->commentRepository->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('The comments does not exit.');
        }

        $comment->setStatus(Comment::APPROVED);
        $this->em->flush();

        return $this->redirectToRoute('admin_dashboard');
    }

    public function CommentChangeStatusDeclined(AdminContext $context)
    {
        $id = $context->getEntity()->getPrimaryKeyValue();
        $comment = $this->commentRepository->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('The comments does not exit.');
        }

        $comment->setStatus(Comment::DECLINED);
        $this->em->flush();

        return $this->redirectToRoute('admin_dashboard');
    }
}
