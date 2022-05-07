<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use DateTime;
use DateTimeZone;
use Lostfocus\Jf2\Utility\Entry;
use PHPUnit\Framework\TestCase;

class UtilitiesTest extends TestCase
{
    public function testEntry(): void
    {
        $published = new DateTime();
        $published->setTimezone(new DateTimeZone('Europe/Berlin'));
        $published->setDate(2022, 01, 01);
        $published->setTime(01, 01);

        $entry = (new Entry())
            ->setPublished($published)
            ->setUrl('https://example.com');

        self::assertSame('entry', (string)$entry->getType());
        self::assertNotNull($entry->getPublished());
        self::assertSame('01', $entry->getPublished()->format('H'));
        self::assertSame('https://example.com', $entry->getUrl());
    }
}