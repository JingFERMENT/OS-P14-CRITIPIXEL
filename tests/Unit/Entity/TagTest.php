<?php

namespace App\Tests\Unit\Entity;

use App\Model\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class TagTest extends KernelTestCase{

    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetCodeIsGeneratedFromName(): void
    {
        $tag = new Tag();
        $tag->setName('Action Game');

        $this->em->persist($tag);
        $this->em->flush();

        $this->assertIsString($tag->getCode());
        $this->assertSame('action-game', $tag->getCode());
    }


}