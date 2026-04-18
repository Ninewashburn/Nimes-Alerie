<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PostVote;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\PostVoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PostVoteController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly PostVoteRepository $postVoteRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/api/posts/{id}/upvote', name: 'api_post_upvote', methods: ['PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function upvote(int $id): JsonResponse
    {
        return $this->handleVote($id, PostVote::VALUE_UP);
    }

    #[Route('/api/posts/{id}/downvote', name: 'api_post_downvote', methods: ['PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function downvote(int $id): JsonResponse
    {
        return $this->handleVote($id, PostVote::VALUE_DOWN);
    }

    private function handleVote(int $postId, int $direction): JsonResponse
    {
        $post = $this->postRepository->find($postId);
        if (!$post) {
            return $this->json(['error' => 'Post not found'], 404);
        }

        /** @var User $user */
        $user = $this->getUser();
        $existing = $this->postVoteRepository->findForUserAndPost($user, $post);

        if ($existing) {
            if ($existing->getValue() === $direction) {
                // Same vote again → cancel
                if (PostVote::VALUE_UP === $direction) {
                    $post->setUpVote(max(0, ($post->getUpVote() ?? 0) - 1));
                } else {
                    $post->setDownVote(max(0, ($post->getDownVote() ?? 0) - 1));
                }
                $this->em->remove($existing);
            } else {
                // Opposite vote → switch
                if (PostVote::VALUE_UP === $direction) {
                    $post->setUpVote(($post->getUpVote() ?? 0) + 1);
                    $post->setDownVote(max(0, ($post->getDownVote() ?? 0) - 1));
                } else {
                    $post->setDownVote(($post->getDownVote() ?? 0) + 1);
                    $post->setUpVote(max(0, ($post->getUpVote() ?? 0) - 1));
                }
                $existing->setValue($direction);
            }
        } else {
            // New vote
            $vote = new PostVote();
            $vote->setUser($user);
            $vote->setPost($post);
            $vote->setValue($direction);
            $this->em->persist($vote);

            if (PostVote::VALUE_UP === $direction) {
                $post->setUpVote(($post->getUpVote() ?? 0) + 1);
            } else {
                $post->setDownVote(($post->getDownVote() ?? 0) + 1);
            }
        }

        $this->em->flush();

        return $this->json([
            'upVote' => $post->getUpVote(),
            'downVote' => $post->getDownVote(),
        ]);
    }
}
