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
        self::assertSame('entry', (string)$jf2->getType());
        $properties = $jf2->getProperties();
        self::assertSame('2015-10-20T15:49:00-0700', (string)$properties['published']);
        self::assertSame('http://example.com/post/fsjeuu8372', (string)$properties['url']);
        self::assertCount(1, $properties['author']);
        $author = $properties['author']->getValue();
        self::assertInstanceOf(Jf2::class, $author);
        self::assertSame('card', (string)$author->getType());
        self::assertCount(1, $properties['category']);
    }

    private function loadExample(string $path): string
    {
        return file_get_contents(__DIR__ . '/samples/' . $path);
    }
}