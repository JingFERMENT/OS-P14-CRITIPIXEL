<?php

namespace App\Tests;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\Attributes\DataProvider;
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

    /**
     * @return iterable<string, array{array<int>, array<string,int>}>
     */
    public static function provideRatings(): iterable
    {

        // no review 
        yield 'no review' => [
            [],
            ['one' => 0, 'two' => 0, 'three' => 0, 'four' => 0, 'five' => 0],
        ];

        // one review 
        yield 'one review 1' => [
            [1],
            ['one' => 1, 'two' => 0, 'three' => 0, 'four' => 0, 'five' => 0],
        ];

        yield 'one review 2' => [
            [2],
            ['one' => 0, 'two' => 1, 'three' => 0, 'four' => 0, 'five' => 0],
        ];

        yield 'one review 3' => [
            [3],
            ['one' => 0, 'two' => 0, 'three' => 1, 'four' => 0, 'five' => 0],
        ];

        yield 'one review 4' => [
            [4],
            ['one' => 0, 'two' => 0, 'three' => 0, 'four' => 1, 'five' => 0],
        ];

        yield 'one review 5' => [
            [5],
            ['one' => 0, 'two' => 0, 'three' => 0, 'four' => 0, 'five' => 1],
        ];

        // multiple reviews
        yield 'several reviews' => [
            [1, 2, 5, 4, 5],
            ['one' => 1, 'two' => 1, 'three' => 0, 'four' => 1, 'five' => 2],
        ];
    }

    /**
    * @param array<string, int> $expected
    */
    private function assertCounts(object $count, array $expected): void
    {
        self::assertSame($expected['one'],   $count->getNumberOfOne());
        self::assertSame($expected['two'],   $count->getNumberOfTwo());
        self::assertSame($expected['three'], $count->getNumberOfThree());
        self::assertSame($expected['four'],  $count->getNumberOfFour());
        self::assertSame($expected['five'],  $count->getNumberOfFive());
    }

   /**
    * @param array<int> $ratings
    * @param array<string, int> $expected
    */
    #[DataProvider('provideRatings')]
    public function testCountRatingsPerValue(array $ratings, array $expected): void
    {

        $videoGame = self::createVideoGame(...$ratings);

        $this->ratingHandler->countRatingsPerValue($videoGame);

        $this->assertCounts(
            $videoGame->getNumberOfRatingsPerValue(),
            $expected
        );
    }
}
