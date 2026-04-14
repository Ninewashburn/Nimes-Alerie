<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    // Non-production fixture. Value is only used inside PHPUnit and never reaches any environment.
    private const TEST_PASSWORD = 'fixture-value-not-a-secret';

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'email'     => 'nouveau@nimes-alerie.gal',
            'password'  => self::TEST_PASSWORD,
            'firstName' => 'Astro',
            'lastName'  => 'Chien',
            'address'   => '42 rue de l\'Orbite',
            'city'      => 'Nîmes',
            'birthAt'   => '1990-01-15',
        ], $overrides);
    }

    public function testRegisterReturnsBadRequestOnEmptyBody(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], '');

        $this->assertResponseStatusCodeSame(400);
    }

    public function testRegisterReturnsBadRequestOnMissingFields(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'incomplet@nimes-alerie.gal',
        ]));

        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testRegisterReturnsBadRequestOnShortPassword(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(
            $this->validPayload(['password' => '123']),
        ));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testRegisterReturnsBadRequestOnInvalidDate(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(
            $this->validPayload(['birthAt' => 'pas-une-date']),
        ));

        $this->assertResponseStatusCodeSame(400);
    }
}
