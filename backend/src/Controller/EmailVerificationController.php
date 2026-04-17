<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;

class EmailVerificationController extends AbstractController
{
    #[Route('/api/verify-email', name: 'api_verify_email', methods: ['GET'])]
    public function verify(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        RateLimiterFactory $apiVerifyEmailLimiter,
    ): JsonResponse {
        $limiter = $apiVerifyEmailLimiter->create($request->getClientIp() ?? 'unknown');
        if (!$limiter->consume()->isAccepted()) {
            return $this->json(['error' => 'Trop de tentatives.'], 429);
        }

        $token = $request->query->get('token');

        if (!$token) {
            return $this->json(['error' => 'Token invalide.'], 400);
        }

        $user = $userRepository->findOneBy(['emailVerifyToken' => $token]);

        if (!$user) {
            return $this->json(['error' => 'Token invalide.'], 400);
        }

        if ($user->isVerified()) {
            return $this->json(['message' => 'Email déjà vérifié.'], 200);
        }

        $user->setIsVerified(true);
        $user->setEmailVerifyToken(null);
        $entityManager->flush();

        return $this->json(['message' => 'Email vérifié avec succès.'], 200);
    }
}
