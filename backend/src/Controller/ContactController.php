<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ContactMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/api/contact', name: 'api_contact', methods: ['POST'])]
    public function send(
        Request $request,
        EntityManagerInterface $em,
        RateLimiterFactory $apiContactLimiter,
    ): JsonResponse {
        $limiter = $apiContactLimiter->create($request->getClientIp());
        if (!$limiter->consume(1)->isAccepted()) {
            return $this->json(['error' => 'Trop de messages envoyés. Réessayez dans une minute.'], 429);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Invalid request body'], 400);
        }

        $email   = trim($data['email']   ?? '');
        $subject = trim($data['subject'] ?? '');
        $message = trim($data['message'] ?? '');

        if (empty($email) || empty($message)) {
            return $this->json(['error' => 'Email et message sont obligatoires.'], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Adresse email invalide.'], 422);
        }

        if (strlen($message) < 10) {
            return $this->json(['error' => 'Le message doit contenir au moins 10 caractères.'], 422);
        }

        if (strlen($message) > 5000) {
            return $this->json(['error' => 'Le message ne peut pas dépasser 5000 caractères.'], 422);
        }

        $contact = new ContactMessage();
        $contact->setEmail($email);
        $contact->setSubject($subject ?: 'general');
        $contact->setMessage($message);

        $em->persist($contact);
        $em->flush();

        return $this->json(['message' => 'Votre message a bien été reçu.'], 201);
    }
}
