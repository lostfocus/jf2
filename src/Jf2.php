<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use Countable;
use JsonException;
use JsonSerializable;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\Jf2PropertyInterface;
use Lostfocus\Jf2\Property\Jf2Collection;
use Lostfocus\Jf2\Property\Jf2Content;
use Lostfocus\Jf2\Property\Jf2Media;
use Lostfocus\Jf2\Property\Jf2References;
use stdClass;
use Stringable;

class Jf2 implements JsonSerializable, Stringable, Countable
{
    /** @var array<Jf2PropertyInterface> */
    private array $properties = [];

    /**
     * @throws Jf2Exception
     */
    public static function fromJsonString(string $jsonString): self
    {
        try {
            return self::fromJsonClass(json_decode($jsonString, false, 512, JSON_THROW_ON_ERROR));
        } catch (JsonException $e) {
            throw new Jf2Exception($e->getMessage(), Jf2Exception::JSON_EXCEPTION, $e);
        }
    }

    /**
     * @throws Jf2Exception
     */
    public static function fromJsonClass(stdClass $json): self
    {
        $jf2 = new self();

        foreach ($json as $key => $value) {
            self::insertProperty($jf2, $key, $value);
        }

        return $jf2;
    }

    /**
     * @param Jf2 $jf2
     * @param array|stdClass|string $value
     * @param bool $update
     * @return static
     * @throws Jf2Exception
     */
    public static function insertType(Jf2 $jf2, array|stdClass|string $value, bool $update = false): self
    {
        if (!is_string($value) || (!$update && array_key_exists('type', $jf2->properties))) {
            throw new Jf2Exception(
                'Type MUST be a single string value only.',
                Jf2Exception::TYPE_MUST_BE_STRING
            );
        }
        $jf2->properties['type'] = Jf2Property::fromString($value);
        return $jf2;
    }

    /**
     * @param Jf2 $jf2
     * @param array|stdClass|string $value
     * @return Jf2
     * @throws Jf2Exception
     */
    public static function insertChildren(Jf2 $jf2, array|stdClass|string $value): Jf2
    {
        if (!is_array($value)) {
            throw new Jf2Exception(
                'Children MUST be an array',
                Jf2Exception::CHILDREN_MUST_BE_ARRAY
            );
        }
        /*
         * In case we have an object as an array
         */
        if (array_key_exists('type', $value)) {
            try {
                $value = [
                    json_decode(
                        json_encode($value, JSON_THROW_ON_ERROR),
                        false,
                        512,
                        JSON_THROW_ON_ERROR)
                ];
            } catch (JsonException $e) {
                throw new Jf2Exception($e->getMessage(), Jf2Exception::JSON_EXCEPTION, $e);
            }
        }

        $jf2->properties['children'] = Jf2Collection::fromArray($value);
        return $jf2;
    }

    /**
     * @param Jf2 $jf2
     * @param array|stdClass|string $value
     * @return Jf2
     * @throws Jf2Exception
     */
    private static function insertReferences(Jf2 $jf2, array|stdClass|string $value): Jf2
    {
        if (!$value instanceof stdClass) {
            throw new Jf2Exception(
                'References MUST be an object',
                Jf2Exception::REFERENCES_MUST_BE_OBJECT
            );
        }
        $jf2->properties['references'] = Jf2References::fromClass($value);
        return $jf2;
    }

    /**
     * @param Jf2 $jf2
     * @param string $key
     * @param array|string|stdClass $value
     * @return Jf2
     * @throws Jf2Exception
     */
    private static function insertMedia(Jf2 $jf2, string $key, array|string|stdClass $value): Jf2
    {
        $jf2->properties[$key] = Jf2Media::fromValue($value);
        return $jf2;
    }

    public function jsonSerialize(): stdClass
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
    public function __toString(): string
    {
        try {
            return json_encode($this, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Jf2Exception($e->getMessage(), Jf2Exception::JSON_EXCEPTION, $e);
        }
    }

    /**
     * @return Jf2PropertyInterface|null
     */
    public function getType(): ?Jf2PropertyInterface
    {
        if (!array_key_exists('type', $this->properties) && array_key_exists('children', $this->properties)) {
            return Jf2Property::fromString('feed');
        }
        return $this->properties['type'] ?? null;
    }

    /**
     * @return Jf2Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function count(): int
    {
        return count($this->properties);
    }

    /**
     * @param Jf2 $jf2
     * @param string $key
     * @param numeric|string|stdClass|array<mixed>|self $value
     * @return static
     * @throws Jf2Exception
     */
    public static function insertProperty(Jf2 $jf2, string $key, array|float|int|string|stdClass|self $value): self
    {
        if(is_int($value) || is_float($value)) {
            $value = (string)$value;
        }

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
                return self::insertType($jf2, $value);
            /*
             * children is a container for all unnamed sub-objects inside an entry.
             * That is, any sub-object that is not associated to a specific property
             * of the object. If a children value is set, it MUST be serialized
             * as an array even if only a single item is present.
             */
            case 'children':
                return self::insertChildren($jf2, $value);
            /*
             * references is an associative array, serialized as a JSON object,
             * of all sub-objects inside an object which have "id" defined as an external [URL].
             * The authoritative source for all objects in this array is always at the URL,
             * not in this object. If the references property is defined, it MUST be
             * serialized as an associative array and MUST be present at the top level entry only.
             */
            case 'references':
                return self::insertReferences($jf2, $value);
            case 'video':
            case 'photo':
                return self::insertMedia($jf2, $key, $value);
            default:
        }

        if (
            array_key_exists($key, $jf2->properties) &&
            $jf2->properties[$key] instanceof Jf2PropertyInterface
        ) {
            $jf2->properties[$key]->addValue($value);
            return $jf2;
        }

        if ($key === 'content') {
            $jf2->properties[$key] = Jf2Content::fromValue($value);
        } elseif (is_array($value)) {
            $jf2->properties[$key] = Jf2Property::fromArray($value);
        } elseif ($value instanceof stdClass) {
            $jf2->properties[$key] = Jf2Property::fromClass($value);
        } elseif ($value instanceof self) {
            $jf2->properties[$key] = Jf2Property::fromJf2($value);
        } else {
            $jf2->properties[$key] = Jf2Property::fromString((string)$value);
        }
        return $jf2;
    }

    /**
     * @throws Jf2Exception
     */
    public function addProperty(string $key, $value): self
    {
        return static::insertProperty($this, $key, $value);
    }

    /**
     * @return array<Jf2>
     */
    public function getChildren(): array
    {
        if (
            !array_key_exists('children', $this->properties) ||
            !$this->properties['children'] instanceof Jf2Collection
        ) {
            return [];
        }
        $children = [];
        foreach ($this->properties['children'] as $child) {
            $children[] = $child;
        }
        return $children;
    }

    /**
     * @throws Jf2Exception
     */
    public static function addChild(self $jf2, stdClass|array $child): self
    {
        if (is_array($child)) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $child = (object)$child;
        }
        if (!array_key_exists('children', $jf2->properties) || !$jf2->properties['children'] instanceof Jf2Collection) {
            return self::insertChildren($jf2, [$child]);
        }
        $jf2->properties['children']->addValue(Jf2Property::fromClass($child));
        return $jf2;
    }
}