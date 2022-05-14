<?php /** @noinspection DuplicatedCode */
declare(strict_types=1);

namespace Lostfocus\Jf2;


use JsonException;
use Lostfocus\Jf2\Interfaces\ObjectInterface;
use Lostfocus\Jf2\Property\Content;
use Lostfocus\Jf2\Property\Item as ItemProperty;
use Lostfocus\Jf2\Property\Media;
use Lostfocus\Jf2\Property\References;
use PHPUnit\Framework\TestCase;

/**
 * @group property
 */
class PropertyTest extends TestCase
{
    /**
     * @throws Exception\Jf2Exception
     */
    public function testString(): void
    {
        $stringValue = 'test';
        $property = Property::fromValue($stringValue);
        self::assertInstanceOf(Property::class, $property);
        self::assertSame($stringValue, (string)$property);
        self::assertSame($stringValue, $property->getValue());
    }

    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testCardAsArray(): void
    {
        $card = [
            'type' => 'card',
            'name' => 'Alice',
            'url' => 'http://alice.example.com',
            'photo' => 'http://alice.example.com/photo.jpg',
        ];
        $property = Property::fromValue($card);
        self::assertInstanceOf(ItemProperty::class, $property);
        $value = $property->getValue();
        self::assertInstanceOf(ObjectInterface::class, $value);
        self::assertSame($card['type'], $value->getType());
        self::assertSame($card['type'], (string)$value->getProperty('type'));
        self::assertSame($card['type'], $value->getProperty('type')->getValue());
        self::assertSame($card['name'], (string)$value->getProperty('name'));
        self::assertSame($card['name'], $value->getProperty('name')->getValue());
        self::assertSame($card['url'], (string)$value->getProperty('url'));
        self::assertSame($card['url'], $value->getProperty('url')->getValue());
        self::assertSame($card['photo'], (string)$value->getProperty('photo'));
        self::assertSame($card['photo'], $value->getProperty('photo')->getValue());
        self::assertCount(4, $property);
        self::assertCount(4, $value);
        /** @var array $testCard */
        $testCard = json_decode(json_encode($property, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        self::assertCount(count($card), $testCard);
        foreach ($card as $key => $value) {
            self::assertArrayHasKey($key, $testCard);
            self::assertSame($value, $testCard[$key]);
        }
    }

    /**
     * @return void
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testStringArray(): void
    {
        $array = ['Likes', 'Posts'];
        $property = Property::fromValue($array);
        self::assertInstanceOf(Property::class, $property);
        self::assertCount(2, $property);
        /** @var array $testArray */
        $testArray = json_decode(json_encode($property, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        self::assertCount(count($array), $testArray);
        foreach ($array as $key => $value) {
            self::assertArrayHasKey($key, $testArray);
            self::assertSame($value, $testArray[$key]);
        }
    }

    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testContentArray(): void
    {
        $array = [
            'html' => '<p>Hello World</p>',
            'text' => 'Hello World',
        ];
        $property = Property::fromValue($array);
        self::assertInstanceOf(Content::class, $property);
        self::assertSame($array['html'], $property->getHtml());
        self::assertSame($array['text'], $property->getText());
        /** @var array $testArray */
        $testArray = json_decode(json_encode($property, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        self::assertCount(count($array), $testArray);
        foreach ($array as $key => $value) {
            self::assertArrayHasKey($key, $testArray);
            self::assertSame($value, $testArray[$key]);
        }
    }

    /**
     * @throws Exception\Jf2Exception
     * @throws JsonException
     */
    public function testMediaArray(): void
    {
        $array = [
            'content-type' => 'video/mp4',
            'url' => 'sample_h264.mov',
        ];
        $property = Property::fromValue($array);
        self::assertInstanceOf(Media::class, $property);
        self::assertSame($array['content-type'], $property->getContentType());
        self::assertSame($array['url'], $property->getUrl());
        /** @var array $testArray */
        $testArray = json_decode(json_encode($property, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        self::assertCount(count($array), $testArray);
        foreach ($array as $key => $value) {
            self::assertArrayHasKey($key, $testArray);
            self::assertSame($value, $testArray[$key]);
        }
    }

    /**
     * @return void
     * @throws Exception\Jf2Exception
     */
    public function testReplaceStringProperty(): void
    {
        $stringValue = 'test';
        $property = Property::fromValue($stringValue);
        self::assertInstanceOf(Property::class, $property);
        self::assertSame($stringValue, (string)$property);
        self::assertSame($stringValue, $property->getValue());
        $newStringValue = 'new';
        $property->replaceValue($newStringValue);
        self::assertInstanceOf(Property::class, $property);
        self::assertSame($newStringValue, (string)$property);
        self::assertSame($newStringValue, $property->getValue());
    }

    /**
     * @return void
     * @throws Exception\Jf2Exception
     */
    public function testReferences(): void
    {
        $referenceArray = [
            'http://bob.example.com/post/100' => [
                'type' => 'entry',
                'published' => '2015-10-18T12:33:00-0700',
                'url' => 'http://bob.example.com/post/100',
                'author' => 'http://bob.example.com',
                'name' => 'My First Post',
                'content' => 'This is my first post on my new blog, I hope you like it',
            ],
            'http://bob.example.com' => [
                'type' => 'card',
                'name' => 'Bob',
                'url' => 'http://bob.example.com',
                'photo' => 'http://bob.example.com/mypicture.jpg',
            ],
        ];
        $property = References::fromValue($referenceArray);
        self::assertInstanceOf(References::class, $property);
        self::assertCount(2, $property);
        foreach ($property as $key => $value) {
            self::assertInstanceOf(ObjectInterface::class, $value);
            self::assertSame($referenceArray[$key]['type'], $value->getType());
        }
    }
}