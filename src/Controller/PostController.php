<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
class PostController extends AbstractController
{
    private EntityManagerInterface $em;
    private PostRepository $postRepository;
    private PaginatorInterface $paginator;

    /**
     * @param EntityManagerInterface $em
     * @param PostRepository $postRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(EntityManagerInterface $em, PostRepository $postRepository, PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->postRepository = $postRepository;
        $this->paginator = $paginator;
    }


    #[Route('/', name: 'post')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/add', name: 'post_add')]
    public function addPost(Request $request): Response
    {
        $postEntity = new Post ();
        $postEntity->setCreatedAt(new DateTime('NOW'));
        $postEntity->setUpVote(0);
        $postEntity->setDownVote(0);

        $user = $this->getUser();
        $postEntity->setUser($user);

        $form = $this->createForm(PostFormType::class, $postEntity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($postEntity);
            $this->em->flush();
            return $this->redirectToRoute('thread_view', ['id' => $postEntity->getThread()->getId()]);
        }

        return $this->render('post/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/view/{id}', name: 'post_view_by_user')]
    public function viewPost(int $id, Request $request): Response
    {
        $queryBuilder = $this->postRepository->getQbAll()
            ->where('post.user = :id')
            ->setParameter(':id', $id);
        $viewPostByUser = $this->paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 3);

        return $this->render('post/view.html.twig', [
            'postView' => $viewPostByUser,
        ]);
    }

    #[Route('/add-upvote/{id}', name: 'upvote_add')]
    public function addUpvote(int $id, Request $request): Response
    {
        $user = $this->getUser();
        $postEntity = $this->postRepository->find($id);

        if($user === null) {
            $this->redirectToRoute('app_login');
        } else {
            $postEntity->setUpVote($postEntity->getUpVote()+1);

            $this->em->persist($postEntity);
            $this->em->flush();
        }

        return $this->redirectToRoute('thread_view', ['id' => $postEntity->getThread()->getId()]);
    }

    #[Route('/add-downvote/{id}', name: 'downvote_add')]
    public function addDownvote(int $id, Request $request): Response
    {
        $user = $this->getUser();
        $postEntity = $this->postRepository->find($id);

        if($user === null) {
            $this->redirectToRoute('app_login');
        } else {
            $postEntity->setDownVote($postEntity->getDownVote()+1);

            $this->em->persist($postEntity);
            $this->em->flush();
        }

        return $this->redirectToRoute('thread_view', ['id' => $postEntity->getThread()->getId()]);
    }

    #[Route('/delete/{id}', name: 'post_delete')]
    public function deleteMovie(Request $request, int $id): Response
    {
        $postEntity = $this->postRepository->find($id);
        $this->em->remove($postEntity);
        $this->em->flush();
        return $this->redirectToRoute('post_view_by_user', ['id' => $postEntity->getUser()->getId()]);
    }
}
