<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
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
    ) {}

    #[Route('/api/posts/{id}/upvote', name: 'api_post_upvote', methods: ['PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function upvote(int $id): JsonResponse
    {
        return $this->applyVote($id, PostVote::VALUE_UP);
    }

    #[Route('/api/posts/{id}/downvote', name: 'api_post_downvote', methods: ['PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function downvote(int $id): JsonResponse
    {
        return $this->applyVote($id, PostVote::VALUE_DOWN);
    }

    private function applyVote(int $postId, int $value): JsonResponse
    {
        $post = $this->postRepository->find($postId);

        if (!$post) {
            return $this->json(['error' => 'Post not found'], 404);
        }

        /** @var User $user */
        $user = $this->getUser();

        $existingVote = $this->postVoteRepository->findForUserAndPost($user, $post);

        if ($existingVote === null) {
            // First vote from this user on this post
            $vote = (new PostVote())
                ->setUser($user)
                ->setPost($post)
                ->setValue($value);
            $this->em->persist($vote);

            $this->adjustCounter($post, $value, +1);
        } elseif ($existingVote->getValue() === $value) {
            // Same button clicked again -> cancel the vote
            $this->adjustCounter($post, $value, -1);
            $this->em->remove($existingVote);
        } else {
            // Switching sides -> remove old counter, add new counter
            $this->adjustCounter($post, $existingVote->getValue(), -1);
            $this->adjustCounter($post, $value, +1);
            $existingVote->setValue($value);
        }

        $this->em->flush();

        return $this->json([
            'upVote'   => $post->getUpVote(),
            'downVote' => $post->getDownVote(),
        ]);
    }

    private function adjustCounter(Post $post, int $value, int $delta): void
    {
        if ($value === PostVote::VALUE_UP) {
            $post->setUpVote(max(0, ($post->getUpVote() ?? 0) + $delta));
        } elseif ($value === PostVote::VALUE_DOWN) {
            $post->setDownVote(max(0, ($post->getDownVote() ?? 0) + $delta));
        }
    }
}
