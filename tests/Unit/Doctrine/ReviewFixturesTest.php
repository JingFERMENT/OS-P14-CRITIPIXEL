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

        $userRepository->expects(self::once())->method('findAll')->willReturn([$user]);

        $videoGameRepository->expects(self::once())->method('findAll')->willReturn([$videoGame]);

        $manager = $this->createMock(objectManager::class);

        $manager->method('getRepository')->willReturnMap([
            [User::class, $userRepository],
            [VideoGame::class, $videoGameRepository]
        ]);

        $manager->expects(self::exactly(5))
            ->method('persist')
            ->with(self::isInstanceOf(Review::class));

        $manager->expects(self::atLeastOnce())->method('flush');
        
        $faker = $this->createMock(Generator::class);
        
        $fixture = new ReviewFixtures($faker);

        $fixture->load($manager);

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
