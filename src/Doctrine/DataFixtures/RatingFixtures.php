<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use App\Rating\RatingHandler;
use App\Security\Voter\VideoGameVoter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class RatingFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(
        private readonly CalculateAverageRating $calculateAverageRating,
        private readonly CountRatingsPerValue $countRatingsPerValue,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $videoGames = $manager->getRepository(VideoGame::class)->findAll();

        foreach($videoGames as $videoGame) {
            
            $this->calculateAverageRating->calculateAverage($videoGame);
            $this->countRatingsPerValue->countRatingsPerValue($videoGame);
                
            $manager->persist($videoGame);
            
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
        VideoGameFixtures::class];
    }
}
