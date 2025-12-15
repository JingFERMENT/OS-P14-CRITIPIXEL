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
        
        $manager->expects($this->exactly(10))
        ->method('persist')
        ->with($this->isInstanceOf(Tag::class));

        $manager
        ->expects($this->once())
        ->method('flush');

        $fixtures = new TagFixtures();

        $fixtures->load($manager);
    
    }
}
