<?php

declare(strict_types=1);

namespace Future\Blog\Core\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Core\Dto\ContactDto;
use Future\Blog\Core\Entity\Contact;
use Future\Blog\Core\Mapper\ContactCreateMapper;

class ContactManager
{
    private EntityManagerInterface $entityManager;

    private ContactCreateMapper $createMapper;

    public function __construct(EntityManagerInterface $entityManager, ContactCreateMapper $createMapper)
    {
        $this->entityManager = $entityManager;
        $this->createMapper = $createMapper;
    }

    /**
     * @param ContactDto[] $contactDto
     */
    public function saveContact(ContactDto $contactDto): Contact
    {
        $post = $this->createMapper->setContact($contactDto);
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }
}
