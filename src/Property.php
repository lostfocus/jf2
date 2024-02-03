<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use JsonSerializable;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\ObjectInterface;
use Lostfocus\Jf2\Interfaces\PropertyInterface;
use Lostfocus\Jf2\Property\Content;
use Lostfocus\Jf2\Property\Item as ItemProperty;
use Lostfocus\Jf2\Property\Media;
use stdClass;

class Property implements PropertyInterface
{
    /**
     * @var array<mixed>
     */
    protected array $value = [];
    protected int $position = 0;

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

    public function key(): int|string
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

    /**
     * @throws Jf2Exception
     */
    public static function fromString(string $value): PropertyInterface
    {
        return (new self())
            ->addValue($value);
    }

    /**
     * @param  array<mixed>  $value
     * @return PropertyInterface
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

        /**
         * This is a media property
         */
        if (array_key_exists('content-type', $value)) {
            return Media::fromArray($value);
        }

        /**
         * This is a content property
         */
        if (array_key_exists('html', $value)) {
            return Content::fromArray($value);
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

        /**
         * This is a content property
         */
        if (property_exists($value, 'html')) {
            return Content::fromClass($value);
        }

        throw new Jf2Exception(
            'Unexpected type: '.gettype($value),
            Jf2Exception::UNEXPECTED_TYPE
        );
    }

    /**
     * @param  array<mixed>|string|stdClass|ObjectInterface  $value
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


    /**
     * @param  PropertyInterface|array<mixed>|string|stdClass|ObjectInterface  $value
     * @return PropertyInterface
     * @throws Jf2Exception
     */
    public function addValue(PropertyInterface|array|string|stdClass|ObjectInterface $value): PropertyInterface
    {
        if (is_string($value) || ($value instanceof ObjectInterface) || ($value instanceof Media)) {
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
        throw new Jf2Exception(
            'Unexpected type: '.gettype($value),
            Jf2Exception::UNEXPECTED_TYPE
        );
    }

    /**
     * @param  PropertyInterface|array<mixed>|string|stdClass|ObjectInterface  $value
     * @return PropertyInterface
     * @throws Jf2Exception
     */
    public function replaceValue(PropertyInterface|array|string|stdClass|ObjectInterface $value): PropertyInterface
    {
        if($value instanceof PropertyInterface) {
            $replacementValue = $value->getValue();
        } else {
            $replacement = self::fromValue($value);
            $replacementValue = $replacement->getValue();
        }
        if (is_array($replacementValue)) {
            $this->value = $replacementValue;
        } else {
            $this->value = [$replacementValue];
        }

        return $this;
    }

    /**
     * @return string|array<mixed>|stdClass|ObjectInterface|PropertyInterface|null
     */
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
    public function jsonSerialize(): mixed
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
    protected function serializeValue(mixed $value): mixed
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