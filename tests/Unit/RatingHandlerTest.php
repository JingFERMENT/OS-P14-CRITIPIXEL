<?php

namespace App\Tests\Unit;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class RatingHandlerTest extends TestCase
{

    public function testAverageWithNoReview(): void 
    {
        $ratingHandler = new RatingHandler();

        $videoGame = new VideoGame(); 

        $ratingHandler->calculateAverage($videoGame);

        self::assertSame(null, $videoGame->getAverageRating());
    }

    public function testAverageWithOneReview(): void 
    {
        $ratingHandler = new RatingHandler();

        $videoGame = new VideoGame();
        $review = new Review();
        $review->setRating(3);
        $videoGame->addReview($review);

        $ratingHandler->calculateAverage($videoGame);

        self::assertSame(3, $videoGame->getAverageRating());
    }

    public function testAverageWithTwoReviews(): void 
    {
        $ratingHandler = new RatingHandler();

        $videoGame = new VideoGame();
        $review1 = new Review();
        $review1->setRating(3);
        $review2 = new Review();
        $review2->setRating(5);
        $videoGame->addReview($review1);
        $videoGame->addReview($review2);

        $ratingHandler->calculateAverage($videoGame);

        self::assertSame(4, $videoGame->getAverageRating());
    }

    public function testAverageWithLotsOfReviews(){

        $ratingHandler = new RatingHandler();

        $videoGame = new VideoGame();

        $review1 = new Review();

        $review1->setRating(3);

        $review2 = new Review();

        $review2->setRating(3); 

        $review3 = new Review();

        $review3->setRating(3); 

        $review4 = new Review();

        $review4->setRating(4); 
        $videoGame->addReview($review1);
        $videoGame->addReview($review2);
        $videoGame->addReview($review3);
        $videoGame->addReview($review4);

        $ratingHandler->calculateAverage($videoGame);

        self::assertSame(4, $videoGame->getAverageRating());
    }


    public function testAverageWithLotsOfReviewsRoundUp(){
        $ratingHandler = new RatingHandler();

        $videoGame = new VideoGame();

        $review1 = new Review();

        $review1->setRating(3);

        $review2 = new Review();

        $review2->setRating(3);

        $review3 = new Review();

        $review3->setRating(4);

        $videoGame->addReview($review1);
        $videoGame->addReview($review2);
        $videoGame->addReview($review3);

        $ratingHandler->calculateAverage($videoGame);

        self::assertSame(4, $videoGame->getAverageRating());

    }
}
