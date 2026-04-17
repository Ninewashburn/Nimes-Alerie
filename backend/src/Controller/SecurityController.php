<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    #[Route('/api/me', name: 'api_me_update', methods: ['PATCH'])]
    public function updateMe(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        ValidatorInterface $validator,
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $data = json_decode($request->getContent(), true) ?? [];

        $fields = ['firstName', 'lastName', 'telephone', 'gender', 'address', 'secondAddress', 'city', 'postalCode', 'country'];
        foreach ($fields as $field) {
            if (\array_key_exists($field, $data)) {
                $setter = 'set'.ucfirst($field);
                $user->$setter($data[$field]);
            }
        }

        if (!empty($data['newPassword'])) {
            if (empty($data['currentPassword']) || !$hasher->isPasswordValid($user, $data['currentPassword'])) {
                return $this->json(['error' => 'Mot de passe actuel incorrect'], 400);
            }
            $user->setPassword($hasher->hashPassword($user, $data['newPassword']));
        }

        $errors = $validator->validate($user);
        if (\count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], 422);
        }

        $em->flush();

        return $this->json(['message' => 'Profil mis à jour']);
    }

    #[Route('/api/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        /* @var User $user */
        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'telephone' => $user->getTelephone(),
            'gender' => $user->getGender(),
            'birthAt' => $user->getBirthAt()?->format('Y-m-d'),
            'address' => $user->getAddress(),
            'secondAddress' => $user->getSecondAddress(),
            'city' => $user->getCity(),
            'postalCode' => $user->getPostalCode(),
            'country' => $user->getCountry(),
        ]);
    }
}
