<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use Lostfocus\Jf2\Exception\Jf2Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

class Jf2Test extends TestCase
{
    /**
     * @throws Jf2Exception
     */
    public function testAddType(): void
    {
        $jf2 = (new Jf2())
            ->addProperty('type', 'entry');
        $this->expectException(Jf2Exception::class);
        $jf2->addProperty('type', 'entry');
    }

    /**
     * @throws Jf2Exception
     */
    public function testArrayType(): void
    {
        $this->expectException(Jf2Exception::class);
        (new Jf2())
            ->addProperty('type', ['entry']);
    }

    /**
     * @throws Jf2Exception
     */
    public function testStdClassType(): void
    {
        $type = new stdClass();
        $type->test = 'entry';
        $this->expectException(Jf2Exception::class);
        (new Jf2())
            ->addProperty('type', $type);
    }


    /**
     * @throws Exception\Jf2Exception
     */
    public function testAddValues(): void
    {
        $jf2 = (new Jf2())
            ->addProperty('type', 'entry');
        $jf2->addProperty('image', 'http://example.com/image1.jpg');

        $jf2Serialize = $jf2->jsonSerialize();
        self::assertCount(2, $jf2Serialize);
        self::assertArrayHasKey('image', $jf2Serialize);
        self::assertIsString($jf2Serialize['image']);

        $jf2->addProperty('image', 'http://example.com/image2.jpg');
        $jf2Serialize2 = $jf2->jsonSerialize();
        self::assertCount(2, $jf2Serialize2);
        self::assertArrayHasKey('image', $jf2Serialize2);
        self::assertIsArray($jf2Serialize2['image']);
        self::assertCount(2, $jf2Serialize2['image']);
    }
}