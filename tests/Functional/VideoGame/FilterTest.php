<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;

final class FilterTest extends FunctionalTestCase
{
    // filter by page / sorting / order 
    #[DataProvider('sortingProvider')]
    public function testShouldFilterVideoGames(
        bool $shouldSubmitForm,
        int $limit,
        string $sorting,
        string $direction,
        string $expectedFirst,
        string $expectedLast
    ): void {
        $this->get('/');
        self::assertResponseIsSuccessful();

        if ($shouldSubmitForm) {
            $this->client->submitForm('Trier', [
                'limit' => $limit,
                'sorting' => $sorting,
                'direction' => $direction
            ], 'GET');
            self::assertResponseIsSuccessful();
        }

        self::assertSelectorCount($limit, 'article.game-card');
        self::assertSelectorTextSame('article.game-card:nth-child(1) h5.game-card-title a', $expectedFirst); // 2 assertions : existe ?  compare ?
        self::assertSelectorTextSame('article.game-card:last-child h5.game-card-title a', $expectedLast); // 2 assertions
    }

    // dataprovider search
    public static function sortingProvider(): array
    {
        return [
            'default sorting' => [
                'shouldSubmitForm' => false,
                'limit' => 10,
                'sorting' => 'ReleaseDate',
                'direction' => 'Descending',
                'expectedFirst' => 'Jeu vidéo 0',
                'expectedLast' => 'Jeu vidéo 9',
            ],
            // change page / order 
            'sorting ascending limit 50' => [
                'shouldSubmitForm' => true,
                'limit' => 50,
                'sorting' => 'ReleaseDate',
                'direction' => 'Ascending',
                'expectedFirst' => 'Jeu vidéo 0',
                'expectedLast' => 'Jeu vidéo 49',
            ],

            // change page / sorting 
            'sorting descending by AverageRating limit 25' => [
                'shouldSubmitForm' => true,
                'limit' => 25,
                'sorting' => 'AverageRating',
                'direction' => 'Descending',
                'expectedFirst' => 'Jeu vidéo 13',
                'expectedLast' => 'Jeu vidéo 36',
            ],
        ];
    }

    // default sorting 
    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        self::assertSelectorTextSame('article.game-card:nth-child(1) h5.game-card-title a', 'Jeu vidéo 0');
        self::assertSelectorTextSame('article.game-card:last-child h5.game-card-title a', 'Jeu vidéo 9');
    }

    // filter by search
    #[DataProvider('searchFilterProvide')]
    public function testShouldFilterVideoGamesBySearch(
        array $formData,
        int $expectedCount,
        ?string $expectedFirstTitle,
        ?string $expectedLastTitle
    ): void {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form[name="filter"]');
        self::assertSelectorExists('input[name="filter[tags][]"]');
        $this->client->submitForm('Filtrer', $formData, 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount($expectedCount, 'article.game-card');

        if ($expectedCount > 0 && $expectedFirstTitle != null) {
            self::assertSelectorTextSame(
                'article.game-card:nth-child(1) h5.game-card-title a',
                $expectedFirstTitle
            );
            self::assertSelectorTextSame(
                'article.game-card:last-child h5.game-card-title a',
                $expectedLastTitle
            );
        }
    }

    // dataprovider tag
    public static function searchFilterProvide(): array
    {
        return [
            'Exact search Jing' => [
                ['filter[search]' => 'Jing'],
                2,
                'Jeu vidéo 0',
                'Jeu vidéo 1'
            ],
            'Case-insensitive search jing' => [
                ['filter[search]' => 'jing'],
                0,
                null,
                null
            ],
            'No result search' => [
                ['filter[search]' => 'Hello'],
                0,
                null,
                null
            ],
            'Empty Search returns all' => [
                ['filter[search]' => ''],
                10,
                'Jeu vidéo 0',
                'Jeu vidéo 9'
            ],
        ];
    }

    // filter by tag
    #[DataProvider('tagFilterProvider')]
    public function testShouldFilterVideoGamesByTag(
        array $tags,
        int $expectedCount,
        string $expectedFirstVideoGame,
        ?string $expectedLastVideoGame
    ): void {
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
        self::assertSelectorTextSame('article.game-card:nth-child(1) h5.game-card-title a', $expectedFirstVideoGame);
        self::assertSelectorTextSame('article.game-card:last-child h5.game-card-title a', $expectedLastVideoGame);
    }

    // dataprovider tag
    public static function tagFilterProvider(): array
    {
        return [
            'One tag' => [
                [
                    0 => '211'
                ],
                9,
                'Jeu vidéo 12',
                'Jeu vidéo 34'
            ],
            'Another tag' => [
                [
                    5 => '216'
                ],
                8,
                'Jeu vidéo 1',
                'Jeu vidéo 24'
            ],
            'several tags' => [
                [
                    0 => '211',
                    3 => '214'
                ],
                3,
                'Jeu vidéo 25',
                'Jeu vidéo 28',
            ],
            'no tag' => [
                [],
                10,
                'Jeu vidéo 0',
                'Jeu vidéo 9'
            ],
            'non existent tag' => [
                [
                    0 => '210'
                ],
                10,
                'Jeu vidéo 0',
                'Jeu vidéo 9'
            ]
        ];
    }
}
