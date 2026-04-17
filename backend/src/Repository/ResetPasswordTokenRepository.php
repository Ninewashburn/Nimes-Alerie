<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ResetPasswordToken;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResetPasswordToken>
 *
 * @method ResetPasswordToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResetPasswordToken|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method ResetPasswordToken[]    findAll()
 * @method ResetPasswordToken[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
class ResetPasswordTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPasswordToken::class);
    }

    public function findValidToken(string $token): ?ResetPasswordToken
    {
        return $this->createQueryBuilder('t')
            ->where('t.token = :token')
            ->andWhere('t.used = false')
            ->andWhere('t.expiresAt > :now')
            ->setParameter('token', $token)
            ->setParameter('now', new DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteTokensForUser(User $user): void
    {
        $this->createQueryBuilder('t')
            ->delete()
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
