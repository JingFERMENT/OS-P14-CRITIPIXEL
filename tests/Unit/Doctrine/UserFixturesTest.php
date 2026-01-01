<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\User;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixturesTest extends TestCase
{
    public function testUserFixturesTestLoad(): void
    {

        $manager = $this->createMock(ObjectManager::class);

        $manager->expects(self::exactly(10))->method('persist')->with(self::isInstanceOf(User::class));

        $userPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $userPasswordHasher->expects(self::exactly(10))
            ->method('hashPassword')
            ->with(self::isInstanceOf(User::class), 'password')
            ->willReturn('hashed_password');

        $manager->expects(self::once())->method('flush');

        $fixtures = new UserFixtures($userPasswordHasher);

        $fixtures->load($manager);
    }
}
