<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

final class RatingFixturesTest extends TestCase
{

    public function testLoad(): void
    {

        $videoGame = new VideoGame();

        $calculateAverageRating = $this->createMock(CalculateAverageRating::class);

        $countRatingsPerValue = $this->createMock(CountRatingsPerValue::class);

        $calculateAverageRating->expects(self::once())
            ->method('calculateAverage')
            ->with($videoGame);

        $countRatingsPerValue->expects(self::once())
            ->method('countRatingsPerValue')
            ->with($videoGame);

        $respository = $this->createMock(ObjectRepository::class);
        $respository->method('findAll')->willReturn([$videoGame]);

        $manager = $this->createMock(ObjectManager::class);

        $manager->method('getRepository')->willReturn($respository);
        $manager->expects(self::once())->method('flush');

        $fixture = new RatingFixtures(
        $calculateAverageRating,
        $countRatingsPerValue
    );
        $fixture->load($manager);
    }

    
   
       public function testGetDependencies(): void
    {
       $calculateAverageRating = $this->createMock(CalculateAverageRating::class);

        $countRatingsPerValue = $this->createMock(CountRatingsPerValue::class);

        $fixture = new RatingFixtures(
        $calculateAverageRating,
        $countRatingsPerValue
    );

       self::assertSame(
            [VideoGameFixtures::class],
            $fixture->getDependencies()
        );
    }
    
}
