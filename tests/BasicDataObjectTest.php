<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;


use JsonException;
use Lostfocus\Jf2\Interfaces\ObjectInterface;
use Lostfocus\Jf2\Property\Item as ItemProperty;
use PHPUnit\Framework\TestCase;

/**
 * @group dataobjects
 */
class BasicDataObjectTest extends TestCase
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
            "type" => "card",
            "name" => "Alice",
            "url" => "http://alice.example.com",
            "photo" => "http://alice.example.com/photo.jpg",
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
        foreach ($card as $key => $value) {
            self::assertArrayHasKey($key, $testCard);
            self::assertSame($value, $testCard[$key]);
        }
    }
}