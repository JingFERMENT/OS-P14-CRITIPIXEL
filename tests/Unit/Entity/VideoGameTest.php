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

        self::assertSame($file, $videoGame->getImageFile());

    }

      public function testSetImageFileNull():void
    {
        $videoGame = new VideoGame();

        $videoGame->setImageFile(null);
        self::assertNull($videoGame->getImageFile());
    }


    public function testGetImageSize(): void
    {
        $videoGame = new VideoGame();
        $reflection = new ReflectionClass($videoGame);
        $property = $reflection->getProperty('imageSize');
        $property->setValue($videoGame, 2_098_872);

        self::assertSame(2_098_872, $videoGame->getImageSize());
    }

    
    public function testGetIdReturnsNullByDefault(): void
    {
        $videoGame = new VideoGame(); // or Tag, Rating, etc.

        self::assertNull($videoGame->getId());
    }

    public function testSetTitleSetsValueAndReturnsSelf(): void
    {
        $videoGame = new VideoGame();

        $result = $videoGame->setTitle('Jeu Vidéo 88');

        self::assertSame($videoGame, $result);

        self::assertSame('Jeu Vidéo 88', $videoGame->getTitle());
    }

    public function testAddTagAddsTag(): void
    {
        $videoGame = new VideoGame();
        $tag = new Tag();

        $result = $videoGame->addTag($tag);

        self::assertSame($videoGame, $result);

        self::assertCount(1, $videoGame->getTags());
        self::assertTrue($videoGame->getTags()->contains($tag));

        $videoGame->addTag($tag);

        self::assertCount(1, $videoGame->getTags());
    }

    public function testRemoveTagRemovesTag(): void
    {
        $videoGame = new VideoGame();
        $tag = new Tag();

        $videoGame->addTag($tag);

        self::assertCount(1, $videoGame->getTags());
        $result = $videoGame->removeTag($tag);

        self::assertSame($videoGame, $result);

        self::assertCount(0, $videoGame->getTags());
        self::assertFalse($videoGame->getTags()->contains($tag));
    }
}
