<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $user = new User();

        $user->setEmail('test@example.com');
        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame('test@example.com', $user->getUserIdentifier());

        $user->setFirstName('John');
        $this->assertSame('John', $user->getFirstName());

        $user->setLastName('Doe');
        $this->assertSame('Doe', $user->getLastName());

        $user->setAddress('123 Main St');
        $this->assertSame('123 Main St', $user->getAddress());

        $user->setCity('Paris');
        $this->assertSame('Paris', $user->getCity());

        $user->setCountry('France');
        $this->assertSame('France', $user->getCountry());

        $user->setTelephone('0612345678');
        $this->assertSame('0612345678', $user->getTelephone());
    }

    public function testRolesAlwaysIncludeRoleUser(): void
    {
        $user = new User();

        $this->assertContains('ROLE_USER', $user->getRoles());

        $user->setRoles(['ROLE_ADMIN']);
        $roles = $user->getRoles();
        $this->assertContains('ROLE_USER', $roles);
        $this->assertContains('ROLE_ADMIN', $roles);
    }

    public function testRolesAreUnique(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER', 'ROLE_USER', 'ROLE_ADMIN']);

        $roles = $user->getRoles();
        $this->assertCount(2, $roles);
    }

    public function testPassword(): void
    {
        $user = new User();
        $user->setPassword('hashed_password');

        $this->assertSame('hashed_password', $user->getPassword());
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        $user->eraseCredentials();

        // Should not throw
        $this->assertTrue(true);
    }
}
