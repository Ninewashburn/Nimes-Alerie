<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private UserPasswordHasherInterface&MockObject $passwordHasher;
    private UserService $userService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->userService = new UserService($this->entityManager, $this->passwordHasher);
    }

    public function testCreateUser(): void
    {
        $user = new User();

        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'plain_password')
            ->willReturn('hashed_password');

        $this->entityManager->expects($this->once())->method('persist')->with($user);
        $this->entityManager->expects($this->once())->method('flush');

        $this->userService->createUser($user, 'plain_password');

        $this->assertSame('hashed_password', $user->getPassword());
    }

    public function testUpdateUserWithPassword(): void
    {
        $user = new User();
        $user->setPassword('old_password');

        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'new_password')
            ->willReturn('new_hashed_password');

        $this->entityManager->expects($this->once())->method('persist')->with($user);
        $this->entityManager->expects($this->once())->method('flush');

        $this->userService->updateUser($user, 'new_password');

        $this->assertSame('new_hashed_password', $user->getPassword());
    }

    public function testUpdateUserWithoutPassword(): void
    {
        $user = new User();
        $user->setPassword('existing_password');

        $this->passwordHasher->expects($this->never())->method('hashPassword');
        $this->entityManager->expects($this->once())->method('persist')->with($user);
        $this->entityManager->expects($this->once())->method('flush');

        $this->userService->updateUser($user);

        $this->assertSame('existing_password', $user->getPassword());
    }

    public function testDeleteUser(): void
    {
        $user = new User();

        $this->entityManager->expects($this->once())->method('remove')->with($user);
        $this->entityManager->expects($this->once())->method('flush');

        $this->userService->deleteUser($user);
    }
}
