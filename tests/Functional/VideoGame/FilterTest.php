<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;

final class FilterTest extends FunctionalTestCase
{
    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

    // test filter by tag
    #[DataProvider('tagFilterProvider')]
    public function testShouldFilterVideoGamesByTag(array $tags, int $expectedCount): void
    {
        $this->get('/');
        // 1. check if the form exists
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form[name="filter"]');
        self::assertSelectorExists('input[name="filter[tags][]"]');

        // 2. vérifier le comportement du filtre
        $query = http_build_query([
            'page' => 1,
            'limit'   => 10,
            'sorting' => 'ReleaseDate',
            'direction' => 'Descending',
            'filter' =>
            [
                'search' => '',
                'tags' => array_values($tags)
            ]
        ]);

        $this->get("/?$query");

        self::assertResponseIsSuccessful();
        self::assertSelectorCount($expectedCount, 'article.game-card');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        // 3 - vérifier la présentation visuelle

    }

    public static function tagFilterProvider(): array
    {
        return [
            'One tag' => [[0 => '211'], 9],
            'Another tag' => [[5 => '216'], 8],
            'several tags' => [[
                0 => '211',
                5 => '216',
            ], 1],
            'no tag' => [[], 10],
            'non existent tag' => [[0 => '210'], 10]
        ];
    }

    // test filter by sort
    #[DataProvider('sortingProvider')]
    public function testShouldSortVideoGames(
        bool $submit,
        int $limit,
        string $sorting,
        string $direction,
        string $expectedFirst,
        string $expectedLast
    ): void {

        $this->get('/');
        self::assertResponseIsSuccessful(); // 1 assertion

        if ($submit) {
            $this->client->submitForm('Trier', [
                'limit' => $limit,
                'sorting' => $sorting,
                'direction' => $direction
            ], 'GET');
            self::assertResponseIsSuccessful();
        }

        self::assertSelectorCount($limit, 'article.game-card'); // 1 assertion
        self::assertSelectorTextSame('article.game-card:nth-child(1) h5.game-card-title a', $expectedFirst); // 2 assertions
        self::assertSelectorTextSame('article.game-card:last-child h5.game-card-title a', $expectedLast); // 2 assertions

    }

    public static function sortingProvider(): array
    {
        return [
            'default sorting' => [
                'submit' => false,
                'limit' => 10,
                'sorting' => 'ReleaseDate',
                'direction' => 'Descending',
                'expectedFirst' => 'Jeu vidéo 0',
                'expectedLast' => 'Jeu vidéo 9',
            ],

            'sorting ascending limit 50' => [
                'submit' => true,
                'limit' => 50,
                'sorting' => 'ReleaseDate',
                'direction' => 'Ascending',
                'expectedFirst' => 'Jeu vidéo 0',
                'expectedLast' => 'Jeu vidéo 49',
            ],

            'sorting descending by AverageRating limit 25' => [
                'submit' => true,
                'limit' => 25,
                'sorting' => 'AverageRating',
                'direction' => 'Descending',
                'expectedFirst' => 'Jeu vidéo 13',
                'expectedLast' => 'Jeu vidéo 36',
            ],
        ];
    }

    



}
