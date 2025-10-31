<?php

namespace App\Tests\Unit;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class RatingHandlerTest extends TestCase
{

    private RatingHandler $ratingHandler;

    protected function setUp(): void
    {
        $this->ratingHandler = new RatingHandler();
    }

    /**
     * @dataProvider provideRatings
     */
    public function testCalculateAverageCanHaveRightAverage(array $ratings, ?int $expectedAverage): void
    {
        $videoGame = $this->createVideoGame(...$ratings);

        $this->ratingHandler->calculateAverage($videoGame);

        self::assertSame($expectedAverage, $videoGame->getAverageRating());
    }


    public static function provideRatings(): array
    {
        return [
            'no review' => [[], null],
            'one review' => [[4], 4],
            'two reviews' => [[5, 1], 3],
            'many reviews' => [[5, 1, 2, 5, 1], 3],
            'many reviews round up' => [[5, 1, 2, 5, 4], 4],
            'limit value' => [[0, 5], 3],
        ];
    }

    private function createVideoGame(int ...$ratings): VideoGame
    {

        $videoGame = new VideoGame();

        foreach ($ratings as $rating) {
            $review = new Review();
            $review->setRating($rating);
            $videoGame->addReview($review);
        }

        return $videoGame;
    }
}
