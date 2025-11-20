<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;


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

    public function testUserCannotPostReviewWithMissingNote(): void
    {
        $this->login('user+0@email.com');
        $crawler = $this->get('/jeu-video-2');
        self::assertResponseIsSuccessful();

        // fill out the form
        $form = $crawler->selectButton('Poster')->form([
            'review[comment]' => 'Super !',
        ]);

        $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorExists('select#review_rating.is-invalid');

        self::assertSelectorTextContains('textarea#review_comment', 'Super !');
    }


    public function testUserCannotPostReviewWithTooLongComments(): void
    {
        $this->login('user+0@email.com');
        $crawler = $this->get('/jeu-video-2');
        self::assertResponseIsSuccessful();

        $tooLongComments = str_repeat('Hello', 600);
        $form = $crawler->selectButton('Poster')->form([
            'review[comment]' => $tooLongComments,
            'review[rating]' => 5,
        ]);

        $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorExists('textarea.form-control.is-invalid');
    }

    public function testGuestCannotSeeOrSubmitReviewForm(): void
    {
        $this->get('/jeu-video-0');

        // Form pas visible pour un invité
        self::assertSelectorNotExists('form[name="review"]');

        $this->post('/jeu-video-0', [
            'review' => [
                'rating' => 5,
                'comment' => 'Super !'
            ]
        ]);

        // Avec symfony, rediriger les utilisateurs non authentifiés vers /auth/login
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        Self::assertResponseRedirects('/auth/login');
    }

    public function testUserCannotPostReviewIfAleadyPostedOne()
    {

        // connecter l'utilisateur
        $this->login('user+0@email.com');

        // l'utilisateur va sur la page
        $this->get('/jeu-video-1');
        self::assertResponseIsSuccessful();

        // Formulaire pas visible pour l'utilisateur déjà posté un review
        self::assertSelectorNotExists('form[name="review"]');

        // vérifier que le review de l'utilisateur est déjà là.
        self::assertSelectorTextContains('div.list-group-item:nth-of-type(4) h3', 'user+0');
        self::assertSelectorTextContains('div.list-group-item:nth-of-type(4) p', 'Null');
        self::assertSelectorTextContains('div.list-group-item:nth-of-type(4) span.value', '2');

        $this->post('/jeu-video-1', [
            'review' => [
                'rating' => 5,
                'comment' => 'Très bien'
            ]
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
