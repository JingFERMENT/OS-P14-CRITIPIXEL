<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use App\Model\Entity\VideoGame;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Faker\Factory;

final class VideoGameFixturesTest extends TestCase
{
    public function testVideoGameFixturesLoad(): void
    {
        $tags = [
            (new Tag())->setName('tag1'),
            (new Tag())->setName('tag2'),
            (new Tag())->setName('tag3'),
            (new Tag())->setName('tag4'),
            (new Tag())->setName('tag5'),
        ];

        $tagRepository = $this->createMock(ObjectRepository::class);
        $tagRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($tags);

        $manager = $this->createMock(ObjectManager::class);

        $manager->expects($this->once())
            ->method('getRepository')
            ->with(Tag::class)
            ->willReturn($tagRepository);

        $manager->expects($this->exactly(50))
            ->method('persist')
            ->with($this->callback(
                static function (VideoGame $videoGame): bool {
                    return $videoGame->getTags()->count() === 3;
                }
            ));

        $manager->expects($this->once())->method('flush');
        $faker = Factory::create('fr_FR');
        $fixtures = new VideoGameFixtures($faker);

        $fixtures->load($manager);

        $this->assertTrue(true);
    }

    public function testGetDependenciesForVideoGameFixtures(): void
    {
        $faker = $this->createMock(Generator::class);

        $fixtures = new VideoGameFixtures($faker);

        self::assertSame(
            [TagFixtures::class],
            $fixtures->getDependencies()
        );
    }
}
