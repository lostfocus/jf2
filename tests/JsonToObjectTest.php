<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use PHPUnit\Framework\TestCase;

class JsonToObjectTest extends TestCase
{
    /**
     * @throws Exception\Jf2Exception
     */
    public function testExample01(): void
    {
        $content = $this->loadExample('jf2/spec-ex-01.json');
        $jf2 = Jf2::fromJsonString($content);
        self::assertCount(7, $jf2);
        self::assertSame('entry', (string)$jf2->getType());
        self::assertSame('entry', $jf2->getType()->getValue());
        $properties = $jf2->getProperties();
        self::assertSame('2015-10-20T15:49:00-0700', (string)$properties['published']);
        self::assertSame('http://example.com/post/fsjeuu8372', (string)$properties['url']);
        self::assertCount(1, $properties['author']);
        $author = $properties['author']->getValue();
        self::assertInstanceOf(Jf2::class, $author);
        self::assertSame('card', (string)$author->getType());
        self::assertCount(1, $properties['category']);
    }

    /**
     * @throws Exception\Jf2Exception
     */
    public function testExample02(): void
    {
        $content = $this->loadExample('jf2/spec-ex-02.json');
        $jf2 = Jf2::fromJsonString($content);
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
        $jf2 = Jf2::fromJsonString($content);
        self::assertCount(4, $jf2);
        self::assertSame('card', (string)$jf2->getType());
    }

    /**
     * @throws Exception\Jf2Exception
     */
    public function testExample04(): void
    {
        $content = $this->loadExample('jf2/spec-ex-04.json');
        $jf2 = Jf2::fromJsonString($content);
        self::assertCount(2, $jf2);
        self::assertSame('entry', (string)$jf2->getType());
        $properties = $jf2->getProperties();
        self::assertArrayHasKey('content', $properties);
        $content = $properties['content'];
        self::assertInstanceOf(Jf2Content::class, $content);
        self::assertNotNull($content->getHtml());
        self::assertNotNull($content->getText());
    }

    private function loadExample(string $path): string
    {
        return file_get_contents(__DIR__ . '/samples/' . $path);
    }
}