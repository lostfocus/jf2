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
        $item = (new Item())
            ->addProperty('type', Property::fromString('entry'));
        self::assertCount(1, $item);
        self::assertSame('entry', $item->getType());
        $this->expectException(Jf2Exception::class);
        $item->addProperty('type', Property::fromString('entry'));

        $item2 = (new Item())
            ->add('type', 'entry');
        self::assertCount(1, $item2);
        self::assertSame('entry', $item2->getType());
        $this->expectException(Jf2Exception::class);
        $item2->add('type', 'entry');

    }

    /**
     * @throws Jf2Exception
     */
    public function testAddChildren(): void
    {
        $child1 = new stdClass();
        $child1->type = 'entry';
        $child2 = new stdClass();
        $child2->type = 'entry';
        $jf2 = (new Item())
            ->addProperty('type', Property::fromString('feed'))
            ->addProperty(
                'children',
                Property::fromArray([
                    $child1,
                    $child2,
                ])
            );
        self::assertCount(2, $jf2);
        self::assertSame('feed', $jf2->getType());
        $children = [];
        if (method_exists($jf2, 'getChildren')) {
            $children = $jf2->getChildren();
        }
        self::assertCount(2, $children);
    }

    /**
     * @throws Jf2Exception
     */
    public function testAddChild(): void
    {
        $child = new stdClass();
        $child->type = 'entry';
        $jf2 = (new Item())
            ->addProperty('type', Property::fromString('feed'));
        $jf2 = $jf2->addChild(Item::fromClass($child));
        self::assertCount(2, $jf2);
        self::assertSame('feed', $jf2->getType());
        $children = $jf2->getChildren();
        self::assertCount(1, $children);
    }

    /**
     * @throws Exception\Jf2Exception
     */
    public function testAddValues(): void
    {
        $jf2 = (new Item())
            ->addProperty('type', Property::fromString('entry'))
            ->addProperty('image', Property::fromString('http://example.com/image1.jpg'));

        $jf2Serialize = $jf2->jsonSerialize();
        self::assertObjectHasProperty('image', $jf2Serialize);
        self::assertIsString($jf2Serialize->image);

        $jf2->addProperty('image', Property::fromString('http://example.com/image2.jpg'));
        $jf2Serialize2 = $jf2->jsonSerialize();
        self::assertObjectHasProperty('image', $jf2Serialize2);
        self::assertIsArray($jf2Serialize2->image);
        self::assertCount(2, $jf2Serialize2->image);
    }
}