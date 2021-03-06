<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use JsonException;
use PHPUnit\Framework\TestCase;

class ObjectToJsonTest extends TestCase
{
    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testExample01Manually(): void
    {
        $content = json_decode(
            $this->loadExample('jf2/spec-ex-01.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $author = (new Item())
            ->addProperty('type', Property::fromValue('card'))
            ->addProperty('name', Property::fromValue('Alice'))
            ->addProperty('url', Property::fromValue('http://alice.example.com'))
            ->addProperty('photo', Property::fromValue('http://alice.example.com/photo.jpg'));

        $jf2 = (new Item())
            ->addProperty('type',Property::fromValue( 'entry'))
            ->addProperty('published', Property::fromValue('2015-10-20T15:49:00-0700'))
            ->addProperty('url', Property::fromValue('http://example.com/post/fsjeuu8372'))
            ->addProperty('author', Property::fromValue($author))
            ->addProperty('name', Property::fromValue('Hello World'))
            ->addProperty('content', Property::fromValue('This is a blog post'))
            ->addProperty('category', Property::fromValue('Posts'))
            ;
        $jf2Array = json_decode(
            json_encode($jf2, JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertSame($content, $jf2Array);
    }

    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testExample01(): void
    {
        $path = 'jf2/spec-ex-01.json';
        $this->assertSerializedArrayIsDecodedArray($path);
    }

    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testExample02(): void
    {
        $path = 'jf2/spec-ex-02.json';
        $this->assertSerializedArrayIsDecodedArray($path);
    }

    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testExample03(): void
    {
        $path = 'jf2/spec-ex-03.json';
        $this->assertSerializedArrayIsDecodedArray($path);
    }
    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testExample04(): void
    {
        $path = 'jf2/spec-ex-04.json';
        $this->assertSerializedArrayIsDecodedArray($path);
    }
    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testExample05(): void
    {
        $path = 'jf2/spec-ex-05.json';
        $this->assertSerializedArrayIsDecodedArray($path);
    }
    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testExample07(): void
    {
        $path = 'jf2/spec-ex-07.json';
        $this->assertSerializedArrayIsDecodedArray($path);

        $content = $this->loadExample($path);
        $jf2 = Item::fromString($content);
        self::assertNotNull($jf2->getReference('http://alice.example.com'));
    }

    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testExample08(): void
    {
        $path = 'jf2/spec-ex-08.json';
        $this->assertSerializedArrayIsDecodedArray($path);
    }
    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testExample09(): void
    {
        $path = 'jf2/spec-ex-09.json';
        $this->assertSerializedArrayIsDecodedArray($path);
    }
    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testExample10(): void
    {
        $path = 'jf2/spec-ex-10.json';
        $this->assertSerializedArrayIsDecodedArray($path);
    }

    private function loadExample(string $path): string
    {
        return file_get_contents(__DIR__ . '/samples/' . $path);
    }

    /**
     * @param string $path
     * @return void
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    private function assertSerializedArrayIsDecodedArray(string $path): void
    {
        $content = $this->loadExample($path);
        $testArray = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $jf2 = Item::fromString($content);
        $jf2Array = json_decode(
            json_encode($jf2, JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertSame($testArray, $jf2Array);
    }
}