<?php

declare(strict_types=1);

namespace Future\Blog\Common\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Future\Blog\Core\Entity\Contact;

class ContactFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 5; $i++) {
            $contact = new Contact();
            $contact->setEmail('some_' . $i . '@email.com');
            $contact->setName('name_' . $i);
            $contact->setMessage('Some text from message_' . $i);
            $manager->persist($contact);
        }
        $manager->flush();
    }
}
