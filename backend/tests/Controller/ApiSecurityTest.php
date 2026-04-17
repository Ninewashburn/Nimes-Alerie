<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Vérifie que les endpoints protégés retournent 401 sans JWT
 * et que les endpoints admin retournent 401/403 pour un utilisateur normal.
 */
class ApiSecurityTest extends WebTestCase
{
    /** @dataProvider protectedEndpointsProvider */
    public function testUnauthenticatedRequestReturns401(string $method, string $uri): void
    {
        $client = static::createClient();
        $client->request($method, $uri, [], [], ['CONTENT_TYPE' => 'application/json']);

        $this->assertContains(
            $client->getResponse()->getStatusCode(),
            [401, 403],
            "Expected 401/403 for {$method} {$uri}, got ".$client->getResponse()->getStatusCode(),
        );
    }

    public static function protectedEndpointsProvider(): array
    {
        return [
            'GET /api/me' => ['GET',    '/api/me'],
            'PATCH /api/me' => ['PATCH',  '/api/me'],
            'GET /api/my-orders' => ['GET',    '/api/my-orders'],
            'POST /api/orders' => ['POST',   '/api/orders'],
            'GET /api/admin/stats' => ['GET',    '/api/admin/stats'],
            'GET /api/users' => ['GET',    '/api/users'],
            'POST /api/articles' => ['POST',   '/api/articles'],
            'POST /api/threads' => ['POST',   '/api/threads'],
            'POST /api/posts' => ['POST',   '/api/posts'],
        ];
    }

    public function testPublicEndpointsAreAccessible(): void
    {
        $client = static::createClient();

        // Products list — public
        $client->request('GET', '/api/products');
        $this->assertResponseStatusCodeSame(200);

        // Articles list — public
        $client->request('GET', '/api/articles');
        $this->assertResponseStatusCodeSame(200);

        // Contact — public POST
        $client->request('POST', '/api/contact', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test@test.com',
            'message' => 'Message de test suffisamment long.',
        ]));
        $this->assertNotSame(401, $client->getResponse()->getStatusCode());
        $this->assertNotSame(403, $client->getResponse()->getStatusCode());
    }

    public function testOrderCreationWithFakeJwtReturns401(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/orders', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer fake.jwt.token',
        ], json_encode(['items' => []]));

        $this->assertContains($client->getResponse()->getStatusCode(), [401, 403]);
    }

    public function testAdminEndpointWithFakeJwtReturns401(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/admin/stats', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer fake.jwt.token',
        ]);

        $this->assertContains($client->getResponse()->getStatusCode(), [401, 403]);
    }
}
