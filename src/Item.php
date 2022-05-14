<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use JsonException;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\ObjectInterface;
use Lostfocus\Jf2\Interfaces\PropertyInterface;
use Lostfocus\Jf2\Property\References;
use stdClass;

class Item implements ObjectInterface
{
    /** @var array<string, PropertyInterface> */
    protected array $properties = [];
    private int $position = 0;

    public function current(): ?PropertyInterface
    {
        if (count($this->properties) < 1) {
            return null;
        }

        return $this->properties[$this->position];
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
        return isset($this->properties[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function count(): int
    {
        return count($this->properties);
    }

    /**
     * @return string
     * @throws Jf2Exception
     */
    public function __toString(): string
    {
        try {
            return json_encode($this, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Jf2Exception($e->getMessage(), Jf2Exception::JSON_EXCEPTION, $e);
        }
    }

    /**
     * @param  string  $value
     * @return ObjectInterface
     * @throws Jf2Exception
     */
    public static function fromString(string $value): ObjectInterface
    {
        try {
            return self::fromClass(json_decode($value, false, 512, JSON_THROW_ON_ERROR));
        } catch (JsonException $e) {
            throw new Jf2Exception($e->getMessage(), Jf2Exception::JSON_EXCEPTION, $e);
        }
    }

    /**
     * @param  array  $value
     * @return ObjectInterface
     * @throws Jf2Exception
     */
    public static function fromArray(array $value): ObjectInterface
    {
        try {
            return self::fromClass(
                json_decode(
                    json_encode($value, JSON_THROW_ON_ERROR),
                    false,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );
        } catch (JsonException $e) {
            throw new Jf2Exception($e->getMessage(), Jf2Exception::JSON_EXCEPTION, $e);
        }
    }

    /**
     * @throws Jf2Exception
     */
    public static function fromClass(stdClass $value): ObjectInterface
    {
        $item = new self();

        $objectVars = get_object_vars($value);

        foreach ($objectVars as $objectKey => $objectValue) {
            $property = match ($objectKey) {
                'references' => References::fromValue($objectValue),
                default => Property::fromValue($objectValue),
            };
            $item->addProperty($objectKey, $property);
        }

        return $item;
    }

    /**
     * @param  array|string|stdClass  $value
     * @return ObjectInterface
     * @throws Jf2Exception
     */
    public static function fromValue(array|string|stdClass $value): ObjectInterface
    {
        if (is_string($value)) {
            return self::fromString($value);
        }
        if (is_array($value)) {
            return self::fromArray($value);
        }

        return self::fromClass($value);
    }

    /**
     * @throws Jf2Exception
     */
    public function addProperty(string $key, PropertyInterface $value): ObjectInterface
    {
        /*
         * Handle reserved properties on an object level
         */
        switch ($key) {
            /*
             * type defines the object classification. In microformats, this is
             * presumed to be an h-* class from the microformats2 vocabulary.
             * Type MUST be a single string value only.
             */
            case 'type':
                return $this->setType($value);
            case 'children':
                return $this->addChildren($value);
        }

        if (!array_key_exists($key, $this->properties)) {
            $this->properties[$key] = $value;
        } else {
            $this->properties[$key] = $this->properties[$key]->addValue($value);
        }

        return $this;
    }

    public function replaceProperty(string $key, PropertyInterface $value): ObjectInterface
    {
        $this->properties[$key] = $value;

        return $this;
    }

    public function getProperty(string $key): ?PropertyInterface
    {
        return $this->properties[$key];
    }

    public function hasProperty(string $key): bool
    {
        return array_key_exists($key, $this->properties);
    }

    public function removeProperty(string $key): ObjectInterface
    {
        if (array_key_exists($key, $this->properties)) {
            unset($this->properties[$key]);
        }

        return $this;
    }

    /** @noinspection PhpMixedReturnTypeCanBeReducedInspection */
    public function jsonSerialize(): mixed
    {
        $serialized = new stdClass();
        foreach ($this->properties as $key => $property) {
            $serialized->$key = $property->jsonSerialize();
        }

        return $serialized;
    }

    /**
     * @throws Jf2Exception
     */
    private function setType(PropertyInterface $value): self
    {
        if (array_key_exists('type', $this->properties) || !$value->isString()) {
            throw new Jf2Exception(
                'Type MUST be a single string value only.',
                Jf2Exception::TYPE_MUST_BE_STRING
            );
        }
        $this->properties['type'] = $value;

        return $this;
    }

    private function addChildren(PropertyInterface $value): self
    {
        if (array_key_exists('children', $this->properties)) {
            $this->properties['children']->addValue($value->getValue());
        } else {
            $this->properties['children'] = $value;
        }

        return $this;
    }

    /**
     * @throws Jf2Exception
     */
    public function addChild(ObjectInterface $value): self
    {
        if (array_key_exists('children', $this->properties)) {
            $this->properties['children']->addValue($value);
        } else {
            return $this->addChildren(Property::fromValue($value));
        }

        return $this;
    }

    public function getChildren(): ?PropertyInterface
    {
        return $this->properties['children'] ?? null;
    }

    public function getType(): ?string
    {
        if (array_key_exists('type', $this->properties)) {
            return (string)$this->properties['type'];
        }

        if(array_key_exists('children', $this->properties)) {
            return 'feed';
        }

        return null;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}