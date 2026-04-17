<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Post;
use App\Entity\Thread;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class ForumService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function createThread(Thread $thread, User $user): void
    {
        $thread->setUser($user);
        $thread->setCreatedAt(new DateTimeImmutable());

        $this->entityManager->persist($thread);
        $this->entityManager->flush();
    }

    public function createPost(Post $post, User $user, Thread $thread): void
    {
        $post->setUser($user);
        $post->setThread($thread);
        $post->setCreatedAt(new DateTimeImmutable());
        $post->setUpVote(0);
        $post->setDownVote(0);

        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    public function upvote(Post $post): void
    {
        $post->setUpVote(($post->getUpVote() ?? 0) + 1);
        $this->entityManager->flush();
    }

    public function downvote(Post $post): void
    {
        $post->setDownVote(($post->getDownVote() ?? 0) + 1);
        $this->entityManager->flush();
    }

    public function deletePost(Post $post): void
    {
        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }
}
