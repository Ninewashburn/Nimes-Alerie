<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function preProcess(string $id, object $object): void
    {
        if (!$object instanceof User) {
            return;
        }

        $object->setPassword(
            $this->passwordHasher->hashPassword($object, $object->getPassword())
        );
    }

    public function postProcess(string $id, object $object): void
    {
    }
}
