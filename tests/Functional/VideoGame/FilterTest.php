<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;

final class FilterTest extends FunctionalTestCase
{
    // prepare the different scenarios for data provider:
    private static function prepareUseCases(
        array $query = [],
        int $expectedCount = 10,
        int $expectedOffsetFrom = 1,
        int $expectedOffsetTo = 10,
        int $expectedTotal = 50,
        ?int $expectedCurrentPageNumber = 1,
        ?array $expectedPaginationLinks = null,
        ?array $expectedVideoGames = null
    ): array {
        if ($expectedCurrentPageNumber != null) {
            // set to the default pagination links
            $expectedPaginationLinks = $expectedPaginationLinks ?? ['1', '2', '3', '4'];
        }

        // add 'Première page', 'Précédent' in the links
        if ($expectedCurrentPageNumber > 1) {
            $expectedPaginationLinks = array_merge(['Première page', 'Précédent'], $expectedPaginationLinks);
        }

        // add 'Suivant', 'Dernière page' in the links
        $lastPage = ceil($expectedTotal / $expectedCount);
        if ($expectedCurrentPageNumber < $lastPage) {
                $expectedPaginationLinks = array_merge($expectedPaginationLinks, ['Suivant', 'Dernière page']);
        }
        
        return [
            'query' => $query,
            'expectedCount' => $expectedCount,
            'expectedOffsetFrom' => $expectedOffsetFrom,
            'expectedOffsetTo' => $expectedOffsetTo,
            'expectedTotal' => $expectedTotal,
            'expectedCurrentPageNumber' => $expectedCurrentPageNumber,
            // if expectedPaginationLinks is null, use []
            'expectedPaginationLinks' => $expectedPaginationLinks ?? [],
            'expectedVideoGames' => $expectedVideoGames ?? array_map(
                fn($index) => sprintf('Jeu vidéo %d', $index),
                range($expectedOffsetFrom - 1, $expectedOffsetTo - 1)
            )
        ];
    }

    // functional test for all (filter, sorting etc )
    public static function providerUserCases(): iterable
    {

        yield 'First Default Page' => self::prepareUseCases();

        yield 'Page 2' => self::prepareUseCases(
            // assign value for a named argument 
            query: ['page' => 2],
            expectedOffsetFrom: 11,
            expectedOffsetTo: 20,
            expectedCurrentPageNumber: 2,
            expectedPaginationLinks: ['1', '2', '3', '4', '5']
        );

        yield 'Last Page' => self::prepareUseCases(
            query: ['page' => 5],
            expectedOffsetFrom: 41,
            expectedOffsetTo: 50,
            expectedCurrentPageNumber: 5,
            expectedPaginationLinks: ['2', '3', '4', '5']
        );

        yield 'First Page, limit 25' => self::prepareUseCases(
            query: ['limit' => 25],
            expectedCount: 25,
            expectedOffsetTo: 25,
            expectedPaginationLinks: ['1', '2']
        );

        yield 'First Page, limit 50' => self::prepareUseCases(
            query: ['limit' => 50],
            expectedCount: 50,
            expectedOffsetTo: 50,
            expectedCurrentPageNumber: null,
            expectedPaginationLinks: [],
        );

        yield 'First Page, sorting by Title' => self::prepareUseCases(
            query: ['sorting' => 'Title'],
            expectedVideoGames: [
                'Jeu vidéo 9',
                'Jeu vidéo 8',
                'Jeu vidéo 7',
                'Jeu vidéo 6',
                'Jeu vidéo 5',
                'Jeu vidéo 49',
                'Jeu vidéo 48',
                'Jeu vidéo 47',
                'Jeu vidéo 46',
                'Jeu vidéo 45',
            ]
        );

        yield 'First Page, sorting by AverageRating' => self::prepareUseCases(
            query: ['sorting' => 'AverageRating'],
            expectedVideoGames: [
                'Jeu vidéo 48',
                'Jeu vidéo 13',
                'Jeu vidéo 7',
                'Jeu vidéo 8',
                'Jeu vidéo 11',
                'Jeu vidéo 37',
                'Jeu vidéo 3',
                'Jeu vidéo 14',
                'Jeu vidéo 15',
                'Jeu vidéo 4',
            ]
        );

        yield 'First Page, sorting by Rating' => self::prepareUseCases(
            query: ['sorting' => 'Rating'],
            expectedVideoGames: [
                'Jeu vidéo 4',
                'Jeu vidéo 9',
                'Jeu vidéo 14',
                'Jeu vidéo 19',
                'Jeu vidéo 24',
                'Jeu vidéo 29',
                'Jeu vidéo 34',
                'Jeu vidéo 39',
                'Jeu vidéo 44',
                'Jeu vidéo 49',
            ]
        );

        yield 'First Page, sorting by Rating Direction Ascending' => self::prepareUseCases(
            query: ['sorting' => 'Rating', 'direction' => 'Ascending'],
            expectedVideoGames: [
                'Jeu vidéo 0',
                'Jeu vidéo 5',
                'Jeu vidéo 10',
                'Jeu vidéo 15',
                'Jeu vidéo 20',
                'Jeu vidéo 25',
                'Jeu vidéo 30',
                'Jeu vidéo 35',
                'Jeu vidéo 40',
                'Jeu vidéo 45',
            ]
        );

         // ------------------ TEST SEARCH FILTERS --------------------
        yield 'First Page, filter by Search' => self::prepareUseCases(
            query: ['filter' => ['search' => 'Jing']],
            expectedCount: 2,
            expectedOffsetTo: 2,
            expectedTotal: 2,
            expectedCurrentPageNumber: null,
            expectedVideoGames: [
                'Jeu vidéo 0',
                'Jeu vidéo 1',
            ],
            expectedPaginationLinks: []
        );

        // ------------------ TEST TAG FILTERS --------------------
         yield 'First Page, filter by one tag' => self::prepareUseCases(
            query: ['filter' => ['tags' => ['211']]],
            expectedCount: 9,
            expectedOffsetTo: 9,
            expectedTotal: 9,
            expectedCurrentPageNumber: null,
            expectedVideoGames: [
                'Jeu vidéo 12',
                'Jeu vidéo 19',
                'Jeu vidéo 23',
                'Jeu vidéo 25',
                'Jeu vidéo 27',
                'Jeu vidéo 28',
                'Jeu vidéo 31',
                'Jeu vidéo 33',
                'Jeu vidéo 34',
            ],
            expectedPaginationLinks: []
        );

        yield 'First Page, filter by two tags' => self::prepareUseCases(
            query: ['filter' => ['tags' => ['211', '214']]],
            expectedCount: 3,
            expectedOffsetTo: 3,
            expectedTotal: 3,
            expectedCurrentPageNumber: null,
            expectedVideoGames: [
                'Jeu vidéo 25',
                'Jeu vidéo 27',
                'Jeu vidéo 28',
            ],
            expectedPaginationLinks: []
        );

        // ------------------ TEST TAG AND SEARCH FILTERS --------------------
        yield 'First Page, filter by search and tag' => self::prepareUseCases(
            query: ['filter' => ['tags' => ['212'], 'search' => 'Jing' ]],
            expectedCount: 1,
            expectedOffsetTo: 1,
            expectedTotal: 1,
            expectedCurrentPageNumber: null,
            expectedVideoGames: [
                'Jeu vidéo 0',
            ],
            expectedPaginationLinks: []
        );

    }

    #[DataProvider('providerUserCases')]
    public function testShouldShowVideoGamesByUsercases(
        array $query,
        int $expectedCount,
        int $expectedOffsetFrom,
        int $expectedOffsetTo,
        int $expectedTotal,
        ?int $expectedCurrentPageNumber,
        ?array $expectedPaginationLinks,
        ?array $expectedVideoGames,
    ): void {
        $this->get('/', $query);
        // check the query 
        self::assertResponseIsSuccessful();
      
        // check the videogame number
        self::assertSelectorCount($expectedCount, 'article.game-card');

        // check the texts 
        self::assertSelectorTextSame('div.fw-bold', sprintf(
            'Affiche %d jeux vidéo de %d à %d sur les %d jeux vidéo',
            $expectedCount,
            $expectedOffsetFrom,
            $expectedOffsetTo,
            $expectedTotal
        ));



        if ($expectedCurrentPageNumber === null) {
            self::assertSelectorNotExists('nav[aria-label="Pagination"]');
        } else {
            // add a string to force $expectedCurrentPageNumber to became a string
            self::assertSelectorTextSame('li.page-item.active', (string)$expectedCurrentPageNumber);
            self::assertSelectorCount(count($expectedPaginationLinks), 'li.page-item');

            foreach ($expectedPaginationLinks as $expectedPaginationLink) {
                self::assertSelectorExists('li.page-item .page-link', $expectedPaginationLink);
            }
        }


        // check the videogames titles
            foreach ($expectedVideoGames as $index => $expectedVideoGame) {
                $number = $index + 1;
                self::assertSelectorTextSame(
                    "article.game-card:nth-child($number) h5.game-card-title a",
                    $expectedVideoGame
                );
            }
        
    }

    // unit test for filtering videogames
    #[DataProvider('filterProvider')]
    public function testShouldFilterVideoGames(
        ?string $search,
        array $tags,
        int $expectedCount,
        ?string $expectedFirstVideoGame,
        ?string $expectedLastVideoGame
    ): void {
        $this->get('/');
        // 1. check if the form exists
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form[name="filter"]');
        self::assertSelectorExists('input[name="filter[tags][]"]');
        self::assertSelectorExists('input[name="filter[search]"]');

        // 2. make the query
        $query = http_build_query([
            'page' => 1,
            'limit'   => 10,
            'sorting' => 'ReleaseDate',
            'direction' => 'Descending',
            'filter' =>
            [
                'search' => $search,
                'tags' => array_values($tags)
            ]
        ]);

        $this->get("/?$query");

        self::assertResponseIsSuccessful();
        self::assertSelectorCount($expectedCount, 'article.game-card');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        // 3 - check the page 
        if ($expectedCount > 0 && $expectedFirstVideoGame != null && $expectedLastVideoGame != null) {
            self::assertSelectorTextSame('article.game-card:nth-child(1) h5.game-card-title a', $expectedFirstVideoGame);
            self::assertSelectorTextSame('article.game-card:last-child h5.game-card-title a', $expectedLastVideoGame);
        }
    }

    public static function filterProvider(): array
    {
        return [
            // ------------------ TAG FILTERS --------------------
            'One tag' => [
                'search' => '',
                'tags' => [211],
                9,
                'Jeu vidéo 12',
                'Jeu vidéo 34'
            ],
            'Another tag' => [
                'search' => '',
                'tags' => [216],
                8,
                'Jeu vidéo 1',
                'Jeu vidéo 24'
            ],
            'several tags' => [
                'search' => '',
                'tags' => [211, 214],
                3,
                'Jeu vidéo 25',
                'Jeu vidéo 28',
            ],
            'no tag' => [
                'search' => '',
                'tags' => [],
                10,
                'Jeu vidéo 0',
                'Jeu vidéo 9'
            ],
            'non existent tag' => [
                'search' => '',
                'tags' => [210],
                10,
                'Jeu vidéo 0',
                'Jeu vidéo 9'
            ],

            // ------------------ SEARCH FILTERS --------------------
            'Exact search Jing' => [
                'search' => 'Jing',
                'tags' => [],
                2,
                'Jeu vidéo 0',
                'Jeu vidéo 1'
            ],
            'Case-insensitive search jing' => [
                'search' => 'jing',
                'tags' => [],
                0,
                null,
                null
            ],
            'No result search' => [
                'search' => 'hello',
                'tags' => [],
                0,
                null,
                null
            ],
            'Empty Search returns all' => [
                'search' => '',
                'tags' => [],
                10,
                'Jeu vidéo 0',
                'Jeu vidéo 9'
            ],
            // ------------------ SEARCH AND TAG FILTERS --------------------
            'Tag and Search filters' => [
                'search' => 'Jing',
                'tags' => [212],
                1,
                'Jeu vidéo 0',
                null
            ],
        ];
    }

    // unit test for sorting videogames
    #[DataProvider('sortingProvider')]
    public function testShouldSortVideoGames(
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
}
