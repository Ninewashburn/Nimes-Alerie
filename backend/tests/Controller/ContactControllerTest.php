<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testSendContactReturnsCreated(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/contact', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'pilote@nimes-alerie.gal',
            'subject' => 'general',
            'message' => 'Bonjour, je voudrais des informations sur vos croquettes galactiques.',
        ]));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $data);
    }

    public function testSendContactReturnsBadRequestOnMissingEmail(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/contact', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'message' => 'Un message sans email.',
        ]));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testSendContactReturnsUnprocessableOnInvalidEmail(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/contact', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'pas-un-email',
            'message' => 'Message avec email invalide.',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testSendContactReturnsBadRequestOnEmptyBody(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/contact', [], [], ['CONTENT_TYPE' => 'application/json'], '');

        $this->assertResponseStatusCodeSame(400);
    }

    public function testSendContactReturnsBadRequestOnShortMessage(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/contact', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'pilote@nimes-alerie.gal',
            'message' => 'Court',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }
}
