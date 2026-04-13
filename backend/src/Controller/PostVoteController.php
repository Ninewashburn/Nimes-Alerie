<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PostVoteController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/api/posts/{id}/upvote', name: 'api_post_upvote', methods: ['PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function upvote(int $id): JsonResponse
    {
        $post = $this->postRepository->find($id);

        if (!$post) {
            return $this->json(['error' => 'Post not found'], 404);
        }

        $post->setUpVote(($post->getUpVote() ?? 0) + 1);
        $this->em->flush();

        return $this->json([
            'upVote'   => $post->getUpVote(),
            'downVote' => $post->getDownVote(),
        ]);
    }

    #[Route('/api/posts/{id}/downvote', name: 'api_post_downvote', methods: ['PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function downvote(int $id): JsonResponse
    {
        $post = $this->postRepository->find($id);

        if (!$post) {
            return $this->json(['error' => 'Post not found'], 404);
        }

        $post->setDownVote(($post->getDownVote() ?? 0) + 1);
        $this->em->flush();

        return $this->json([
            'upVote'   => $post->getUpVote(),
            'downVote' => $post->getDownVote(),
        ]);
    }
}
