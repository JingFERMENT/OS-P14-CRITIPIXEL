<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use App\Model\Entity\VideoGame;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Faker\Factory;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker
    ) {}

    public function load(ObjectManager $manager): void
    {
        // $tags = $manager->getRepository(Tag::class)->findAll();
        $baseDate = new DateTimeImmutable('2024-01-01 12:00:00'); // fixed => deterministic tests

        foreach (range(0, 49) as $index) {
            $description = $this->faker->paragraphs(10, true);
            // Put "Jing" only in the first 2 video games (index 0 and 1)
            if ($index < 2) {
                $description = "Jing - " . $description;
            }

            $releaseDate = $baseDate->modify(sprintf('-%d days', $index)); // 0 newest -> 49 oldest

            $videoGame = (new VideoGame())
                ->setTitle(sprintf('Jeu vidéo %d', $index))
                ->setDescription($description)
                ->setReleaseDate($releaseDate)
                ->setTest($this->faker->paragraphs(6, true))
                ->setRating(($index % 5) + 1)
                ->setImageName(sprintf('video_game_%d.png', $index))
                ->setImageSize(2_098_872);

            $this->assignTagsToVideogame($videoGame, $manager);

            $manager->persist($videoGame);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [TagFixtures::class];
    }

    private function assignTagsToVideogame(VideoGame $game, ObjectManager $manager): void
    {
        $title = $game->getTitle();

        $theMagicMatrix = [
            'Jeu vidéo 12' => ['tag+9'],
            'Jeu vidéo 19' => ['tag+0', 'tag+5', 'tag+9'],
            'Jeu vidéo 23' => ['tag+9'],
            'Jeu vidéo 25' => ['tag+9'],
            'Jeu vidéo 27' => ['tag+9'],
            'Jeu vidéo 1' => ['tag+0'],
            'Jeu vidéo 2' => ['tag+1'],
            'Jeu vidéo 3' => ['tag+1'],
        ];

        if (!array_key_exists($title, $theMagicMatrix)) {
            return;
        }
        $tagNames = $theMagicMatrix[$title];

        // assigner les tags à ce vidéo game
        foreach ($tagNames as $tagName) {
            $tag = $manager->getRepository(Tag::class)->findOneBy(['name' => $tagName]);
            $game->addTag($tag);
        }
    }
}
