<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use PHPUnit\Framework\TestCase;

class Jf2Test extends TestCase
{
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