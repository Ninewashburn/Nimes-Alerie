<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        $requiredFields = ['email', 'password', 'firstName', 'lastName', 'address', 'city', 'birthAt'];
        $missing = array_filter($requiredFields, fn(string $f) => empty($data[$f]));
        if ($missing) {
            return $this->json(['error' => 'Missing required fields: ' . implode(', ', $missing)], 400);
        }

        $plainPassword = $data['password'];
        if (strlen($plainPassword) < 6) {
            return $this->json(['error' => 'Password must be at least 6 characters'], 400);
        }

        $birthAt = \DateTime::createFromFormat('Y-m-d', $data['birthAt']);
        if (!$birthAt) {
            return $this->json(['error' => 'Invalid date format. Use YYYY-MM-DD'], 400);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setAddress($data['address']);
        $user->setCity($data['city']);
        $user->setBirthAt($birthAt);

        if (!empty($data['telephone'])) {
            $user->setTelephone($data['telephone']);
        }
        if (!empty($data['country'])) {
            $user->setCountry($data['country']);
        }
        if (!empty($data['secondAddress'])) {
            $user->setSecondAddress($data['secondAddress']);
        }

        $user->setPassword(
            $passwordHasher->hashPassword($user, $plainPassword)
        );

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 422);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ], 201);
    }
}
