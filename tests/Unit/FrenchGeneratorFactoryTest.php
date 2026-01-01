<?php

use App\Faker\FrenchGeneratorFactory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

final class FrenchGeneratorFactoryTest extends TestCase
{
    public function testCreateReturnsFakerGenerator(): void
    {
        $generator = FrenchGeneratorFactory::create();
        $name = $generator->lastName();
        self::assertNotEmpty($name);
    }
}
