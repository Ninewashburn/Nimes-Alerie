<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $article = new Article();

        $article->setName('Découverte d\'une nébuleuse');
        $this->assertSame('Découverte d\'une nébuleuse', $article->getName());

        $article->setContent('Contenu du rapport de mission...');
        $this->assertSame('Contenu du rapport de mission...', $article->getContent());
    }

    public function testIdIsNullByDefault(): void
    {
        $article = new Article();
        $this->assertNull($article->getId());
    }

    public function testNullableFields(): void
    {
        $article = new Article();
        $this->assertNull($article->getName());
        $this->assertNull($article->getContent());
        $this->assertNull($article->getUser());
    }

    public function testUserRelation(): void
    {
        $article = new Article();
        $user    = new User();

        $article->setUser($user);
        $this->assertSame($user, $article->getUser());

        $article->setUser(null);
        $this->assertNull($article->getUser());
    }
}
