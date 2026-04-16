<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Blocks login for users who have not yet confirmed their email address.
 *
 * Invoked by the `login` firewall via `user_checker:` in security.yaml.
 * Runs before password verification so the timing is independent of password validity.
 */
final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException(
                'Veuillez vérifier votre adresse email avant de vous connecter.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // No-op: we only enforce email verification before auth succeeds.
    }
}
