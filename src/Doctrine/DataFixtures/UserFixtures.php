<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher) {
        
    }


    public function load(ObjectManager $manager): void
    {
        foreach (range(0, 9) as $index) {
            $user = (new User())
                ->setEmail(sprintf('user+%d@email.com', $index))
                ->setPlainPassword('password')
                ->setUsername(sprintf('user+%d', $index));

            $hashedPassword = $this->userPasswordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);  // Set hashed password instead of plain password
            $manager->persist($user);
        }

        $manager->flush();
    }
}
