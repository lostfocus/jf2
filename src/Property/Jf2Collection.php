<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Property;

use Iterator;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\Jf2PropertyInterface;
use Lostfocus\Jf2\Jf2;
use Lostfocus\Jf2\Jf2Property;
use stdClass;

class Jf2Collection extends Jf2Property implements Iterator
{
    private int $position = 0;

    /**
     * @param array $value
     * @return Jf2PropertyInterface
     * @throws Jf2Exception
     */
    public static function fromArray(array $value): Jf2PropertyInterface
    {
        $property = new self();
        foreach ($value as $item) {
            if (!$item instanceof stdClass) {
                throw new Jf2Exception('Property must be a JF2 object', Jf2Exception::PROPERTY_SHOULD_BE_CLASS);
            }
            if (!property_exists($item, 'type')) {
                throw new Jf2Exception('Class should have a type', Jf2Exception::CLASS_SHOULD_HAVE_A_TYPE);
            }
            $property->addValue(self::fromClass($item));
        }
        return $property;
    }

    /**
     * @return array<Jf2>
     * @throws Jf2Exception
     */
    public function getValue(): array
    {
        $value = [];
        /** @var Jf2PropertyInterface $item */
        foreach ($this->value as $item) {
            $itemValue = $item->getValue();
            if (!$itemValue instanceof Jf2) {
                throw new Jf2Exception('Collection should only have objects', Jf2Exception::COLLECTION_SHOULD_ONLY_HAVE_OBJECTS);
            }
            $value[] = $itemValue;
        }
        return $value;
    }

    /**
     * @return Jf2
     * @throws Jf2Exception
     */
    public function current(): Jf2
    {
        $current = $this->value[$this->position];
        if ($current instanceof Jf2PropertyInterface) {
            $current = $current->getValue();
        }
        if (!$current instanceof Jf2) {
            throw new Jf2Exception('Collection should only have objects', Jf2Exception::COLLECTION_SHOULD_ONLY_HAVE_OBJECTS);
        }
        return $current;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->value[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}