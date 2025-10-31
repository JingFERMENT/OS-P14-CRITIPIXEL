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

    public function testCountRatingsPerValueNoReview(): void
    {
        $videoGame = new VideoGame();

        $this->ratingHandler->countRatingsPerValue($videoGame);

        $count = $videoGame->getNumberOfRatingsPerValue();

        self::assertSame(0, $count->getNumberOfOne());
        self::assertSame(0, $count->getNumberOfTwo());
        self::assertSame(0, $count->getNumberOfThree());
        self::assertSame(0, $count->getNumberOfFour());
        self::assertSame(0, $count->getNumberOfFive());

    }

    public function testCountRatingsPerValueOneReview(){

        $videoGame = new VideoGame();

        $this->ratingHandler->countRatingsPerValue($videoGame);

        $review = new Review();

        $review->setRating(2);
        $videoGame->addReview($review);

        $videoGame->getNumberOfRatingsPerValue()->increaseTwo();

        $count = $videoGame->getNumberOfRatingsPerValue();
        self::assertSame(1, $count->getNumberOfTwo());

    }


    public function testCountRatingsPerValueMultipleReviews(){

        $videoGame = new VideoGame();
        $this->ratingHandler->countRatingsPerValue($videoGame);

        $review1 = new Review();

        $review1->setRating(2);
        $videoGame->addReview($review1);


        $review2 = new Review();

        $review2->setRating(5);
        $videoGame->addReview($review2);

        $videoGame->getNumberOfRatingsPerValue()->increaseTwo();
        $videoGame->getNumberOfRatingsPerValue()->increaseFive();

        $count = $videoGame->getNumberOfRatingsPerValue();

        self::assertSame(1, $count->getNumberOfTwo());
        self::assertSame(1, $count->getNumberOfFive());

    }
    
}
