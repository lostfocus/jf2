<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use JsonSerializable;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\ObjectInterface;
use Lostfocus\Jf2\Interfaces\PropertyInterface;
use Lostfocus\Jf2\Property\Item as ItemProperty;
use Lostfocus\Jf2\Property\Media;
use RuntimeException;
use stdClass;

class Property implements PropertyInterface
{
    protected array $value = [];
    private int $position = 0;

    public function current(): mixed
    {
        if (count($this->value) < 1) {
            return null;
        }

        return $this->value[$this->position];
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

    public function count(): int
    {
        return count($this->value);
    }

    /**
     * @return string
     * @throws Jf2Exception
     */
    public function __toString(): string
    {
        if (count($this->value) > 1) {
            throw new Jf2Exception('Unable to cast to string', Jf2Exception::UNABLE_TO_CAST_TO_STRING);
        }
        if (count($this->value) < 1) {
            return '';
        }

        return (string)$this->value[0];
    }

    public static function fromString(string $value): PropertyInterface
    {
        return (new self())
            ->addValue($value);
    }

    /**
     * @throws Jf2Exception
     */
    public static function fromArray(array $value): PropertyInterface
    {
        /**
         * This is an item
         */
        if (array_key_exists('type', $value)) {
            return (new ItemProperty())
                ->addValue(Item::fromArray($value));
        }

        $property = new self();
        foreach ($value as $item) {
            $property->addValue(self::fromValue($item));
        }

        return $property;
    }

    /**
     * @throws Jf2Exception
     */
    public static function fromClass(stdClass $value): PropertyInterface
    {
        /**
         * This is an item
         */
        if (property_exists($value, 'type')) {
            return (new self())
                ->addValue(Item::fromClass($value));
        }

        /**
         * This is a media property
         */
        if (property_exists($value, 'content-type')) {
            return Media::fromClass($value);
        }
        throw new RuntimeException('oh no');
    }

    /**
     * @param  array|string|stdClass|ObjectInterface  $value
     * @return PropertyInterface
     * @throws Jf2Exception
     */
    public static function fromValue(array|string|stdClass|ObjectInterface $value): PropertyInterface
    {
        if (is_array($value)) {
            return self::fromArray($value);
        }
        if (is_string($value)) {
            return self::fromString($value);
        }
        if ($value instanceof stdClass) {
            return self::fromClass($value);
        }

        return (new self())
            ->addValue($value);
    }


    public function addValue(PropertyInterface|array|string|stdClass|ObjectInterface $value): PropertyInterface
    {
        if (is_string($value) || ($value instanceof ObjectInterface)) {
            $this->value[] = $value;

            return $this;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                $this->addValue($item);
            }

            return $this;
        }

        if ($value instanceof PropertyInterface) {
            $this->value[] = $value->getValue();

            return $this;
        }

        throw new RuntimeException('oh no');
    }

    public function replaceValue(PropertyInterface|array|string|stdClass|ObjectInterface $value): PropertyInterface
    {
        throw new RuntimeException('oh no');
    }

    public function getValue(): string|array|stdClass|ObjectInterface|PropertyInterface|null
    {
        if (count($this->value) < 1) {
            return null;
        }
        if (count($this->value) === 1) {
            return $this->value[0];
        }

        return $this->value;
    }

    /**
     * @throws Jf2Exception
     */
    public function jsonSerialize()
    {
        if (count($this->value) < 1) {
            return null;
        }
        if (count($this->value) === 1) {
            $onlyValue = $this->value[0];

            return $this->serializeValue($onlyValue);
        }
        $returnArray = [];
        foreach ($this->value as $key => $value) {
            $returnArray[$key] = $this->serializeValue($value);
        }

        return $returnArray;
    }

    public function isString(): bool
    {
        return (count($this->value) === 1 && is_string($this->value[0]));
    }

    /**
     * @param  mixed  $value
     * @return mixed
     * @throws Jf2Exception
     */
    private function serializeValue(mixed $value): mixed
    {
        if (is_string($value) || is_numeric($value) || ($value === null)) {
            return $value;
        }
        if ($value instanceof JsonSerializable) {
            return $value->jsonSerialize();
        }
        throw new Jf2Exception('Unable to serialize value', Jf2Exception::UNABLE_TO_SERIALIZE_VALUE);
    }
}