<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class ReviewFixturesTest extends TestCase
{
    public function testLoadReviewFixturesTest():void
    {
        $user = $this->createMock(User::class);
        $videoGame = $this->createMock(VideoGame::class);

        $userRepository = $this->createMock(ObjectRepository::class);

        $videoGameRepository = $this->createMock(ObjectRepository::class);

        $userRepository->expects($this->once())->method('findAll')->willReturn([$user]);

        $videoGameRepository->expects($this->once())->method('findAll')->willReturn([$videoGame]);

        $manager = $this->createMock(objectManager::class);

        $manager->method('getRepository')->willReturnMap([
            [User::class, $userRepository],
            [VideoGame::class, $videoGameRepository]
        ]);

        $manager->expects($this->exactly(5))
            ->method('persist')
            ->with($this->isInstanceOf(Review::class));

        $manager->expects($this->atLeastOnce())->method('flush');
        
        $faker = $this->createMock(Generator::class);
        
        $fixture = new ReviewFixtures($faker);

        $fixture->load($manager);

        $this->assertTrue(true);

    }

    public function testGetDependencies(): void
    {
        $faker = $this->createMock(Generator::class);
        $fixtures = new ReviewFixtures($faker);

        self::assertSame(
            [
                UserFixtures::class,
                VideoGameFixtures::class,
            ],
            $fixtures->getDependencies()
        );
    }
}
