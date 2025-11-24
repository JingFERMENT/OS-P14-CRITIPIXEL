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
            'page'=> 1,
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

        // 3 - vérifier la pagination et le tri (page/limit/sort/direction)
        // le nombre affiché doit correspond à ce qui est dans la base des données 
        // ce sont bien les bonnes vidéo qui sont affichés 

        // 4 - vérifier la présentation visuelle
        
    }

    public static function tagFilterProvider(): array
    {
        return [
            'One tag' => [[0 => '211'], 9],
            'Another tag' => [[5 => '216'], 8],
            'several tags' => [[
                0 =>'211',
                5 =>'216',
            ], 1],
            'no tag' => [[], 10],
            'non existent tag' => [[0 =>'210'],10]
        ];
    }
 
}
