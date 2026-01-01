<?php
namespace App\Tests\Unit\Entity;

use App\Model\Entity\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase{

    public function testGetIdReturnsNullByDefault(): void
{
    $user = new User(); // or Tag, Rating, etc.

    self::assertNull($user->getId());
}
}