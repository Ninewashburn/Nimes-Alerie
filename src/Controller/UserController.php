<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', name: 'user_index')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $userRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'user_new')]
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->createFormFromEntity($request, new User());
    }

    #[Route('/show/{id}', name: 'user_show')]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit/{id}', name: 'user_edit')]
    public function edit(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager, User $user): Response
    {
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        $this->addFlash('message', 'Profil mis à jour !');
        return $this->createFormFromEntity($request, $user, 'user/edit.html.twig');
    }

    #[Route('/delete/{id}', name: 'user_delete')]
    public function delete(User $user): Response
    {
        $user = $this->getUser();
        $this->container->get('security.token_storage')->setToken(null);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return $this->redirectToRoute('home');
    }

    private function createFormFromEntity(Request $request, User $user, string $template = 'user/new.html.twig'): Response
    {
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->redirectToRoute('user_index');
        }
        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }
}
