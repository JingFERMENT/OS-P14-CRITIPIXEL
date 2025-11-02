<?php

namespace App\Tests;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class RatingsPerValueCalculatorTest extends TestCase
{
    private RatingHandler $ratingHandler;

    protected function setUp(): void
    {
        $this->ratingHandler = new RatingHandler();
    }

    /**
     * create a VideoGame with variable number of ratings
     */
    private static function createVideoGame(int ...$ratings): VideoGame
    {

        $videoGame = new VideoGame();

        foreach ($ratings as $rating) {
            $review = new Review();
            $review->setRating($rating);
            $videoGame->addReview($review);
        }
        return $videoGame;
    }

    public static function provideRatings(): array
    {
        return [
            'rating 1' => [1, 'getNumberOfOne'],
            'rating 2' => [2, 'getNumberOfTwo'],
            'rating 3' => [3, 'getNumberOfThree'],
            'rating 4' => [4, 'getNumberOfFour'],
            'rating 5' => [5, 'getNumberOfFive']
        ];
    }

    /* no review */
    public function testCountRatingsPerValueNoReview(): void
    {
        $videoGame = self::createVideoGame();

        $this->ratingHandler->countRatingsPerValue($videoGame);

        $count = $videoGame->getNumberOfRatingsPerValue();

        self::assertSame(0, $count->getNumberOfOne());
        self::assertSame(0, $count->getNumberOfTwo());
        self::assertSame(0, $count->getNumberOfThree());
        self::assertSame(0, $count->getNumberOfFour());
        self::assertSame(0, $count->getNumberOfFive());
    }

     /**
     * @dataProvider provideRatings
     */
    public function testCountRatingsPerValueOneReview(int $rating, string $getter)
    {

        $videoGame = self::createVideoGame($rating);

        $this->ratingHandler->countRatingsPerValue($videoGame);

        $count = $videoGame->getNumberOfRatingsPerValue();

        self::assertSame(1, $count->{$getter}());

        $allGetters = [
            'getNumberOfOne',
            'getNumberOfTwo',
            'getNumberOfThree',
            'getNumberOfFour',
            'getNumberOfFive'
        ];

        foreach ($allGetters as $oneGetter) {
            if ($oneGetter != $getter) {
                self::assertSame(0, $count->{$oneGetter}());
            }
        }
    }

    /* mutiple reviews*/
    public function testCountRatingsPerValueMultipleReviews():void
    {

        $videoGame = self::createVideoGame(1,2,5,4,5);

        $this->ratingHandler->countRatingsPerValue($videoGame);

        $count = $videoGame->getNumberOfRatingsPerValue();

        self::assertSame(1, $count->getNumberOfOne());
        self::assertSame(1, $count->getNumberOfTwo());
        self::assertSame(0, $count->getNumberOfThree());
        self::assertSame(1, $count->getNumberOfFour());
        self::assertSame(2, $count->getNumberOfFive());
    }
}
