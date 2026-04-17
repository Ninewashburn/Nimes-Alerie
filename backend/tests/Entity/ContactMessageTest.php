<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ContactMessage;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ContactMessageTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $msg = new ContactMessage();

        $this->assertFalse($msg->isRead());
        $this->assertInstanceOf(DateTimeImmutable::class, $msg->getCreatedAt());
    }

    public function testCreatedAtIsSetOnConstruct(): void
    {
        $before = new DateTimeImmutable();
        $msg = new ContactMessage();
        $after = new DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $msg->getCreatedAt());
        $this->assertLessThanOrEqual($after, $msg->getCreatedAt());
    }

    public function testGettersAndSetters(): void
    {
        $msg = new ContactMessage();

        $msg->setEmail('contact@nimes-alerie.gal');
        $this->assertSame('contact@nimes-alerie.gal', $msg->getEmail());

        $msg->setSubject('order');
        $this->assertSame('order', $msg->getSubject());

        $msg->setMessage('Mon colis n\'est pas arrivé depuis 3 cycles lunaires.');
        $this->assertSame('Mon colis n\'est pas arrivé depuis 3 cycles lunaires.', $msg->getMessage());

        $msg->setRead(true);
        $this->assertTrue($msg->isRead());
    }
}
