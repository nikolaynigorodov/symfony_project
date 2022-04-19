<?php

declare(strict_types=1);

namespace Future\Blog\Common\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Future\Blog\User\Dto\UserRegistration;
use Future\Blog\User\UserManager\UserManager;

class UserFixture extends Fixture
{
    private UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public static function getReferenceKey($i)
    {
        return sprintf('user_%s', $i);
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $userRegistrationDto = new UserRegistration();
            $email = 'user' . $i . '@example.com';
            $userRegistrationDto->setEmail($email);
            $userRegistrationDto->setFirstName('First Name_' . $i);
            $userRegistrationDto->setLastName('Last Name_' . $i);
            $userRegistrationDto->setPlainPassword('1234');
            $newUser = $this->userManager->create($userRegistrationDto, true);
            $this->addReference(self::getReferenceKey($i), $newUser);
        }
    }
}
