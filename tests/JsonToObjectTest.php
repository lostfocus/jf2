<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use Lostfocus\Jf2\Property\Content;
use Lostfocus\Jf2\Property\References;
use PHPUnit\Framework\TestCase;

class JsonToObjectTest extends TestCase
{
    /**
     * @throws Exception\Jf2Exception
     */
    public function testExample01(): void
    {
        $content = $this->loadExample('jf2/spec-ex-01.json');
        $jf2 = Item::fromString($content);
        self::assertCount(7, $jf2);
        self::assertSame('entry', $jf2->getType());
        $properties = $jf2->getProperties();
        self::assertSame('2015-10-20T15:49:00-0700', (string)$properties['published']);
        self::assertSame('http://example.com/post/fsjeuu8372', (string)$properties['url']);
        self::assertCount(1, $properties['author']);
        $author = $properties['author']->getValue();
        self::assertInstanceOf(Item::class, $author);
        self::assertSame('card', (string)$author->getType());
        self::assertCount(1, $properties['category']);
    }

    /**
     * @throws Exception\Jf2Exception
     */
    public function testExample02(): void
    {
        $content = $this->loadExample('jf2/spec-ex-02.json');
        $jf2 = Item::fromString($content);
        self::assertCount(6, $jf2);
        self::assertSame('entry', (string)$jf2->getType());
        $properties = $jf2->getProperties();
        self::assertArrayHasKey('like-of', $properties);
        self::assertCount(2, $properties['category']);
    }

    /**
     * @throws Exception\Jf2Exception
     */
    public function testExample03(): void
    {
        $content = $this->loadExample('jf2/spec-ex-03.json');
        $jf2 = Item::fromString($content);
        self::assertCount(4, $jf2);
        self::assertSame('card', (string)$jf2->getType());
    }

    /**
     * @throws Exception\Jf2Exception
     */
    public function testExample04(): void
    {
        $content = $this->loadExample('jf2/spec-ex-04.json');
        $jf2 = Item::fromString($content);
        self::assertCount(2, $jf2);
        self::assertSame('entry', (string)$jf2->getType());
        $properties = $jf2->getProperties();
        self::assertArrayHasKey('content', $properties);
        $content = $properties['content'];
        self::assertInstanceOf(Content::class, $content);
        self::assertNotNull($content->getHtml());
        self::assertNotNull($content->getText());
    }

    /**
     * @throws Exception\Jf2Exception
     */
    public function testExample05(): void
    {
        $content = $this->loadExample('jf2/spec-ex-05.json');
        $jf2 = Item::fromString($content);
        self::assertCount(2, $jf2);
        self::assertSame('entry', (string)$jf2->getType());
        $properties = $jf2->getProperties();
        self::assertArrayHasKey('video', $properties);
        self::assertCount(3, $properties['video']);
    }

    /**
     * @throws Exception\Jf2Exception
     * @noinspection DuplicatedCode
     */
    public function testExample07(): void
    {
        $content = $this->loadExample('jf2/spec-ex-07.json');
        $jf2 = Item::fromString($content);
        self::assertCount(8, $jf2);
        self::assertSame('entry', (string)$jf2->getType());
        $properties = $jf2->getProperties();
        self::assertArrayHasKey('references', $properties);
        $references = $properties['references'];
        self::assertInstanceOf(References::class, $references);
        self::assertNotNull($references->getReference('http://alice.example.com'));
        $alice = $references->getReference('http://alice.example.com');
        self::assertInstanceOf(Item::class, $alice);
        self::assertSame('card', (string)$alice->getType());
    }

    /**
     * @throws Exception\Jf2Exception
     * @noinspection DuplicatedCode
     */
    public function testExample08(): void
    {
        $content = $this->loadExample('jf2/spec-ex-08.json');
        $jf2 = Item::fromString($content);
        self::assertCount(7, $jf2);
        self::assertSame('entry', (string)$jf2->getType());
        $properties = $jf2->getProperties();
        self::assertCount(1, $properties['author']);
        $author = $properties['author']->getValue();
        self::assertInstanceOf(Item::class, $author);
        self::assertSame('card', (string)$author->getType());
        self::assertArrayHasKey('like-of', $properties);
        self::assertCount(2, $properties['category']);

        self::assertArrayHasKey('references', $properties);
        $references = $properties['references'];
        self::assertInstanceOf(References::class, $references);
        self::assertCount(2, $references);

        self::assertNotNull($references->getReference('http://bob.example.com'));
        $bobCard = $references->getReference('http://bob.example.com');
        self::assertInstanceOf(Item::class, $bobCard);
        self::assertSame('card', (string)$bobCard->getType());

        self::assertNotNull($references->getReference('http://bob.example.com/post/100'));
        $bobPost = $references->getReference('http://bob.example.com/post/100');
        self::assertInstanceOf(Item::class, $bobPost);
        self::assertSame('entry', (string)$bobPost->getType());
    }

    /**
     * @throws Exception\Jf2Exception
     * @noinspection DuplicatedCode
     */
    public function testExample09(): void
    {
        $content = $this->loadExample('jf2/spec-ex-09.json');
        $jf2 = Item::fromString($content);
        self::assertCount(5, $jf2);
        $properties = $jf2->getProperties();
        self::assertArrayHasKey('children', $properties);
        $children = $properties['children'];
        self::assertCount(2, $children);
        foreach ($children as $child) {
            self::assertInstanceOf(Item::class, $child);
            self::assertInstanceOf(Item::class, $child);
        }
    }

    /**
     * @throws Exception\Jf2Exception
     * @noinspection DuplicatedCode
     */
    public function testExample10(): void
    {
        $content = $this->loadExample('jf2/spec-ex-10.json');
        $jf2 = Item::fromString($content);
        self::assertCount(1, $jf2);
        self::assertSame('feed', $jf2->getType());
        $properties = $jf2->getProperties();
        self::assertArrayHasKey('children', $properties);
        $children = $properties['children'];
        self::assertCount(2, $children);
        foreach ($children as $child) {
            self::assertInstanceOf(Item::class, $child);
            self::assertInstanceOf(Item::class, $child);
        }
    }

    private function loadExample(string $path): string
    {
        return file_get_contents(__DIR__ . '/samples/' . $path);
    }
}