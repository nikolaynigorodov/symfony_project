<?php

declare(strict_types=1);

namespace Future\Blog\Core\Mapper;

use Future\Blog\Core\Dto\ContactDto;
use Future\Blog\Core\Entity\Contact;

class ContactCreateMapper
{
    public function setContact(ContactDto $contactDto): Contact
    {
        $contact = new Contact();
        $contact->setEmail($contactDto->getEmail())
            ->setName($contactDto->getName())
            ->setMessage($contactDto->getMessage())
        ;

        return $contact;
    }
}
