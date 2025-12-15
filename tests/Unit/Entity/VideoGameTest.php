<?php

namespace App\Tests\Unit\Entity;

use App\Model\Entity\Tag;
use App\Model\Entity\VideoGame;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\File\File;

class VideoGameTest extends TestCase
{

    public function testSetAndGetImageFile():void
    {
        $videoGame = new VideoGame();

        $file = $this->createMock(File::class);

        $videoGame->setImageFile($file);

        $this->assertSame($file, $videoGame->getImageFile());

    }

      public function testSetImageFileNull():void
    {
        $videoGame = new VideoGame();

        $videoGame->setImageFile(null);
        $this->assertNull($videoGame->getImageFile());
    }


    public function testGetImageSize(): void
    {
        $videoGame = new VideoGame();
        $reflection = new ReflectionClass($videoGame);
        $property = $reflection->getProperty('imageSize');
        $property->setValue($videoGame, 2_098_872);

        $this->assertSame(2_098_872, $videoGame->getImageSize());
    }

    
    public function testGetIdReturnsNullByDefault(): void
    {
        $videoGame = new VideoGame(); // or Tag, Rating, etc.

        $this->assertNull($videoGame->getId());
    }

    public function testSetTitleSetsValueAndReturnsSelf(): void
    {
        $videoGame = new VideoGame();

        $result = $videoGame->setTitle('Jeu Vidéo 88');

        $this->assertSame($videoGame, $result);

        $this->assertSame('Jeu Vidéo 88', $videoGame->getTitle());
    }

    public function testAddTagAddsTag(): void
    {
        $videoGame = new VideoGame();
        $tag = new Tag();

        $result = $videoGame->addTag($tag);

        $this->assertSame($videoGame, $result);

        $this->assertCount(1, $videoGame->getTags());
        $this->assertTrue($videoGame->getTags()->contains($tag));

        $videoGame->addTag($tag);

        $this->assertCount(1, $videoGame->getTags());
    }

    public function testRemoveTagRemovesTag(): void
    {
        $videoGame = new VideoGame();
        $tag = new Tag();

        $videoGame->addTag($tag);

        $this->assertCount(1, $videoGame->getTags());
        $result = $videoGame->removeTag($tag);

        $this->assertSame($videoGame, $result);

        $this->assertCount(0, $videoGame->getTags());
        $this->assertFalse($videoGame->getTags()->contains($tag));
    }
}
