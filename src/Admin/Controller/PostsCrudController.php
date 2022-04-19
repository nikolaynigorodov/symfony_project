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
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Repository\PostRepository;

class PostsCrudController extends AbstractCrudController
{
    private PostRepository $postRepository;

    private EntityManagerInterface $em;

    public function __construct(PostRepository $postRepository, EntityManagerInterface $em)
    {
        $this->postRepository = $postRepository;
        $this->em = $em;
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id'),
            TextField::new('title'),
            TextField::new('summary'),
            TextField::new('content'),
            ChoiceField::new('status', 'Status')->setChoices(Post::SEARCH_POST_STATUS),
            AssociationField::new('category'),
            AssociationField::new('owner'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $statusPostBlocked = Action::new('blocked', 'Blocked', 'btn btn-danger')
            ->linkToCrudAction('postChangeStatusBlocked')
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $statusPostBlocked)

            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)

            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ;
    }

    public function postChangeStatusBlocked(AdminContext $context)
    {
        $id = $context->getEntity()->getPrimaryKeyValue();
        $post = $this->postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('The post does not exit.');
        }

        $post->setStatus(Post::POST_STATUS_BLOCKED);
        $this->em->flush();

        $this->get('session')->getFlashBag()->add('success', 'admin.posts.crud.update_post_blocked');

        return $this->redirectToRoute('admin_dashboard');
    }
}
