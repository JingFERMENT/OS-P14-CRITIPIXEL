<?php

use App\Faker\FrenchGeneratorFactory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

final class FrenchGeneratorFactoryTest extends TestCase
{
    public function testCreateReturnsFakerGenerator(): void
    {
        $generator = FrenchGeneratorFactory::create();

        $this->assertInstanceOf(Generator::class, $generator);

        $name = $generator->lastName();
        $this->assertIsString($name);
        $this->assertNotEmpty($name);
    }
}
