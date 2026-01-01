<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

final class TagFixturesTest extends TestCase
{

    public function testLoad(): void
    {
        $manager = $this->createMock(ObjectManager::class);
        
        $manager->expects(self::exactly(10))
        ->method('persist')
        ->with(self::isInstanceOf(Tag::class));

        $manager
        ->expects(self::once())
        ->method('flush');

        $fixtures = new TagFixtures();

        $fixtures->load($manager);
    
    }
}
