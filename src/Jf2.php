<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use JsonException;
use JsonSerializable;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Stringable;

class Jf2 implements JsonSerializable, Stringable
{
    /** @var Jf2Property[] */
    private array $properties = [];

    /**
     * @throws Jf2Exception
     */
    public static function fromJsonString(string $jsonString): self
    {
        try {
            return self::fromJsonArray(json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR));
        } catch (JsonException $e) {
            throw new Jf2Exception($e->getMessage(), Jf2Exception::JSON_EXCEPTION, $e);
        }
    }

    /**
     * @throws Jf2Exception
     */
    public static function fromJsonArray(array $json): self
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
            } else {
                $jf2->properties[$key] = is_array($value) ? Jf2Property::fromArray($value) : Jf2Property::fromString((string)$value);
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
     * @return Jf2Property|null
     */
    public function getType(): ?Jf2Property
    {
        return $this->properties['type'] ?? null;
    }

    /**
     * @return Jf2Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}