<?php

namespace App\Controller;

use App\Entity\Thread;
use App\Form\ThreadFormType;
use App\Repository\ThreadRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/thread')]
class ThreadController extends AbstractController
{
    private EntityManagerInterface $em;
    private ThreadRepository $threadRepository;
    private PaginatorInterface $paginator;

    /**
     * @param EntityManagerInterface $em
     * @param ThreadRepository $threadRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(EntityManagerInterface $em, ThreadRepository $threadRepository, PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->threadRepository = $threadRepository;
        $this->paginator = $paginator;
    }


    #[Route('/', name: 'thread')]
    public function index(): Response
    {
        return $this->render('thread/index.html.twig', [
            'controller_name' => 'ThreadController',
        ]);
    }

    #[Route('/add', name: 'thread_add')]
    public function addThread(Request $request): Response
    {
        $threadEntity = new Thread ();
        $threadEntity->setCreatedAt(new DateTime('NOW'));

        $user = $this->getUser();
        $threadEntity->setUser($user);

        $form = $this->createForm(ThreadFormType::class, $threadEntity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($threadEntity);
            $this->em->flush();
            return $this->redirectToRoute('subtype_view', ['id' => $threadEntity->getSubtype()->getId()]);
        }

        return $this->render('thread/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/view/{id}', name: 'thread_view')]
    public function viewThread(int $id, Request $request): Response
    {
        $queryBuilder = $this->threadRepository->getQbAll()
            ->where('thread.id = :id')
            ->setParameter(':id', $id);
        $threadView = $this->paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 5);

        return $this->render('thread/view.html.twig', [
            'threadView' => $threadView,
        ]);
    }
}
