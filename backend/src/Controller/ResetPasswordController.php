<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ResetPasswordToken;
use App\Repository\ResetPasswordTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;

class ResetPasswordController extends AbstractController
{
    #[Route('/api/reset-password/request', name: 'api_reset_password_request', methods: ['POST'])]
    public function request(
        Request $request,
        UserRepository $userRepository,
        ResetPasswordTokenRepository $resetPasswordTokenRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        RateLimiterFactory $apiResetPasswordRequestLimiter,
    ): JsonResponse {
        $limiter = $apiResetPasswordRequestLimiter->create($request->getClientIp() ?? 'unknown');
        if (!$limiter->consume()->isAccepted()) {
            return $this->json(['message' => 'Si cet email existe, un lien a été envoyé.'], 200);
        }

        $data = json_decode($request->getContent(), true);

        if (empty($data['email'])) {
            return $this->json(['message' => 'Si cet email existe, un lien a été envoyé.'], 200);
        }

        $user = $userRepository->findOneBy(['email' => $data['email']]);

        if (!$user) {
            return $this->json(['message' => 'Si cet email existe, un lien a été envoyé.'], 200);
        }

        // Delete old tokens for this user
        $resetPasswordTokenRepository->deleteTokensForUser($user);

        // Create new token
        $resetToken = new ResetPasswordToken();
        $resetToken->setUser($user);
        $entityManager->persist($resetToken);
        $entityManager->flush();

        $frontendUrl = $this->getParameter('frontend_url');
        $resetLink = $frontendUrl.'/reset-password?token='.$resetToken->getToken();

        $email = (new Email())
            ->from('noreply@nimes-alerie.gal')
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->text(
                "Bonjour {$user->getFirstName()},\n\n"
                ."Vous avez demandé la réinitialisation de votre mot de passe.\n\n"
                ."Cliquez sur le lien suivant pour le réinitialiser (valable 1 heure) :\n"
                .$resetLink."\n\n"
                ."Si vous n'avez pas fait cette demande, ignorez cet email.\n\n"
                ."L'équipe Nimes-Algérie"
            );

        $mailer->send($email);

        return $this->json(['message' => 'Si cet email existe, un lien a été envoyé.'], 200);
    }

    #[Route('/api/reset-password/confirm', name: 'api_reset_password_confirm', methods: ['POST'])]
    public function confirm(
        Request $request,
        ResetPasswordTokenRepository $resetPasswordTokenRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        RateLimiterFactory $apiResetPasswordConfirmLimiter,
    ): JsonResponse {
        $limiter = $apiResetPasswordConfirmLimiter->create($request->getClientIp() ?? 'unknown');
        if (!$limiter->consume()->isAccepted()) {
            return $this->json(['error' => 'Trop de tentatives.'], 429);
        }

        $data = json_decode($request->getContent(), true);

        if (empty($data['token']) || empty($data['password'])) {
            return $this->json(['error' => 'Token et mot de passe requis.'], 400);
        }

        $resetToken = $resetPasswordTokenRepository->findValidToken($data['token']);

        if (!$resetToken) {
            return $this->json(['error' => 'Token invalide ou expiré.'], 400);
        }

        if (\strlen($data['password']) < 8 || !preg_match('/[A-Z]/', $data['password']) || !preg_match('/[0-9]/', $data['password'])) {
            return $this->json(['error' => 'Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.'], 400);
        }

        $user = $resetToken->getUser();
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        $resetToken->setUsed(true);

        $entityManager->flush();

        return $this->json(['message' => 'Mot de passe réinitialisé.'], 200);
    }
}
