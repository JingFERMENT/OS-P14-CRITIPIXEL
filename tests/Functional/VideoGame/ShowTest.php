<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use function PHPUnit\Framework\assertContains;

final class ShowTest extends FunctionalTestCase
{
    public function testShouldShowVideoGame(): void
    {
        $this->get('/jeu-video-0');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Jeu vidéo 0');
    }

    public function testUserCanPostValidReview(): void
    {
        $this->login('user+0@email.com');
        $crawler = $this->get('/jeu-video-0');
        self::assertResponseIsSuccessful();

        // fill out the form
        $form = $crawler->selectButton('Poster')->form([
            'review[rating]' => 5,
            'review[comment]' => 'Très bon vidéo game !',
        ]);

        $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        self::assertSelectorTextContains('div.list-group-item:last-child h3', 'user+0');
        self::assertSelectorTextContains('div.list-group-item:last-child p', 'Très bon vidéo game !');
        self::assertSelectorTextContains('div.list-group-item:last-child span.value', '5');

    }
}
