<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/article')]
class ArticleController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'articles_index')]
    public function index(ArticleRepository $articleRepository): Response
    {

        return $this->render('articles/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'articles_new')]
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->createFormFromEntity($request, new Article());
    }

    #[Route('/show/{name}', name: 'articles_show')]
    public function show(Article $article): Response
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/edit/{name}', name: 'articles_edit')]
    public function edit(Request $request, Article $article): Response
    {
        $user = $this->getUser();
        if ($user === null) {
/*            $this->addFlash('message', 'Connexion réussie !');*/
            return $this->redirectToRoute('app_login');
        }

        return $this->createFormFromEntity($request, $article, 'articles/edit.html.twig');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/{name}', name: 'articles_delete')]
    public function delete(Article $article): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();
        return $this->redirectToRoute('articles_index');
    }

    private function createFormFromEntity(Request $request, Article $article, string $template = 'articles/new.html.twig'): Response
    {
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($article);
            $this->entityManager->flush();
            return $this->redirectToRoute('articles_index');
        }
        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }
}

//Pour améliorer le tout il faudrait que l'utilisateur concerner puisse effacer son post et pas celui d'autrui.
//Faire aussi en sorte que ça redirige sur la page en cours et pas l'index ?
//Avec des fonctions et des roles : role_user role_admin role_super_admin ?
