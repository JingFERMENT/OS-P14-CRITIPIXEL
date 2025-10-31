<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class ReviewFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(
        private readonly Generator $faker
    ) {}

    public function load(ObjectManager $manager): void
    {

        $users = $manager->getRepository(User::class)->findAll();
        $videoGames = $manager->getRepository(VideoGame::class)->findAll();

        foreach($videoGames as $videoGame) {
            
            for($i=0; $i<5; $i++) {
                $review = (new Review())
                    ->setComment($this->faker->sentence())
                    ->setRating(rand(1,5))
                    ->setUser($users[array_rand($users)])
                    ->setVideoGame($videoGame);
                $manager->persist($review);
            }
            $manager->flush();
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class,
        VideoGameFixtures::class];
    }
}
