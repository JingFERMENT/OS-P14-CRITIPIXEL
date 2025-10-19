<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class TagFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {

        foreach (range(0, 9) as $index) {
            $tag = (new Tag())
                ->setName(sprintf('tag+%d', $index));

            $manager->persist($tag);
        }

        $manager->flush();
    }
}
