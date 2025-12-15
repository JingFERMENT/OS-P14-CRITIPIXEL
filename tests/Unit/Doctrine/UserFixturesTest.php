<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\User;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function PHPUnit\Framework\isInstanceOf;

final class UserFixturesTest extends TestCase
{
    public function testUserFixturesTestLoad(){

        $manager = $this->createMock(ObjectManager::class);

        $manager->expects($this->exactly(10))->method('persist')->with(isInstanceOf(User::class));

        $userPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $userPasswordHasher->expects($this->exactly(10))
            ->method('hashPassword')
            ->with(isInstanceOf(User::class), 'password')->willReturn('hashed_password');
        
        $manager->expects($this->once())->method('flush');
        
        $fixtures = new UserFixtures($userPasswordHasher);

        $fixtures->load($manager);

        $this->assertTrue(true);
    }
}
