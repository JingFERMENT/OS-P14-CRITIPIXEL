<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class ReviewFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * Numéro du jeu => somme des 5 notes
     * (moyenne = somme / 5)
     */
    private const TOP_SUMS = [
        48 => 25, // 5.0
        13 => 24, // 4.8
        7  => 23, // 4.6
        8  => 22, // 4.4
        11 => 21, // 4.2
        37 => 20, // 4.0
        3  => 19, // 3.8
        14 => 18, // 3.6
        15 => 17, // 3.4
        4  => 16, // 3.2
    ];

    public function __construct(
        private readonly Generator $faker
    ) {}

    public function load(ObjectManager $manager): void
    {

        $this->faker->seed(1234);
        mt_srand(1234);
        
        $users = $manager->getRepository(User::class)->findAll();
        $videoGames = $manager->getRepository(VideoGame::class)->findAll();

        foreach ($videoGames as $videoGame) {
            $number = $this->extractNumber($videoGame->getTitle());

            if ($number !== null && isset(self::TOP_SUMS[$number])) {
                // On force les notes pour garantir le top 10
                $ratings = $this->ratingsFromSum(self::TOP_SUMS[$number]);
            } else {
                // Les autres jeux restent "bas" pour ne jamais entrer dans le top 10
                $ratings = array_map(
                    fn() => $this->faker->numberBetween(1, 3),
                    range(1, 5)
                );
            }

            foreach ($ratings as $rating) {
                $review = (new Review())
                    ->setComment($this->faker->sentence())
                    ->setRating($rating)
                    ->setUser($users[array_rand($users)])
                    ->setVideoGame($videoGame);

                $manager->persist($review);
            }

            $manager->flush();
        }

        $manager->flush();
    }

    private function extractNumber(string $title): ?int
    {
        // Récupère le nombre à la fin de "Jeu vidéo 48"
        return preg_match('/(\d+)$/', $title, $m) ? (int) $m[1] : null;
    }

     /**
     * Retourne 5 notes entre 1 et 5 dont la somme vaut $sum
     * @return list<int>
     */
    private function ratingsFromSum(int $sum): array
    {
        // On part de [5,5,5,5,5] (somme 25) et on réduit jusqu'à atteindre $sum
        $ratings = [5, 5, 5, 5, 5];
        $toRemove = 25 - $sum;

        for ($i = 4; $i >= 0 && $toRemove > 0; $i--) {
            $dec = min($toRemove, $ratings[$i] - 1); // on ne descend jamais sous 1
            $ratings[$i] -= $dec;
            $toRemove -= $dec;
        }

        if ($toRemove !== 0) {
            throw new \LogicException('Somme impossible avec 5 notes entre 1 et 5.');
        }

        return $ratings;
    }


    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            VideoGameFixtures::class
        ];
    }
}
