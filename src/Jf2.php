<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use Countable;
use JsonException;
use JsonSerializable;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\Jf2PropertyInterface;
use Lostfocus\Jf2\Property\Jf2Content;
use Lostfocus\Jf2\Property\Jf2Video;
use stdClass;
use Stringable;

class Jf2 implements JsonSerializable, Stringable, Countable
{
    /** @var array<Jf2PropertyInterface|array> */
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
            if ($key === 'type') {
                if (!is_string($value)) {
                    throw new Jf2Exception(
                        'Type MUST be a single string value only.',
                        Jf2Exception::TYPE_MUST_BE_STRING
                    );
                }
                $jf2->properties['type'] = Jf2Property::fromString($value);
            } elseif ($key === 'content') {
                $jf2->properties[$key] = Jf2Content::fromValue($value);
            } elseif ($key === 'video') {
                $jf2->properties[$key] = Jf2Video::fromValue($value);
            } elseif ($key === 'references') {
                if (!array_key_exists($key, $jf2->properties)) {
                    $jf2->properties[$key] = [];
                }
                foreach ($value as $refKey => $refValue) {
                    if (is_array($refValue)) {
                        $jf2->properties[$key][$refKey] = Jf2Property::fromArray($refValue);
                    } elseif ($refValue instanceof stdClass) {
                        $jf2->properties[$key][$refKey] = Jf2Property::fromClass($refValue);
                    } else {
                        $jf2->properties[$key][$refKey] = Jf2Property::fromString((string)$refValue);
                    }
                }
            } elseif (is_array($value)) {
                $jf2->properties[$key] = Jf2Property::fromArray($value);
            } elseif ($value instanceof stdClass) {
                $jf2->properties[$key] = Jf2Property::fromClass($value);
            } else {
                $jf2->properties[$key] = Jf2Property::fromString((string)$value);
            }
        }

        return $jf2;
    }

    public function jsonSerialize(): array
    {
        return [];
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
}