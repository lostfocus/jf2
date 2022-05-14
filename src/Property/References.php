<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Property;

use JsonException;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\ObjectInterface;
use Lostfocus\Jf2\Interfaces\PropertyInterface;
use Lostfocus\Jf2\Property;
use stdClass;

class References extends Property
{
    /** @var array<string, ObjectInterface> */
    private array $references = [];

    public static function fromArray(array $value): PropertyInterface
    {
        $references = new self();
        foreach ($value as $key => $itemContent) {
            $item = Property::fromValue($itemContent);
            if ($item instanceof Item) {
                $references->references[$key] = $item->getValue();
            }
        }

        return $references;
    }

    public static function fromClass(stdClass $value): PropertyInterface
    {
        try {
            return self::fromArray(
                json_decode(json_encode($value, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR)
            );
        } catch (JsonException $e) {
            throw new Jf2Exception($e->getMessage(), Jf2Exception::JSON_EXCEPTION, $e);
        }
    }

    /**
     * @throws Jf2Exception
     */
    public static function fromValue(array|string|stdClass|ObjectInterface $value): PropertyInterface
    {
        if (is_array($value)) {
            return self::fromArray($value);
        }
        if ($value instanceof stdClass) {
            return self::fromClass($value);
        }

        return parent::fromValue($value);
    }

    public function getReference($key): ?ObjectInterface
    {
        return $this->references[$key] ?? null;
    }

    public function jsonSerialize(): ?array
    {
        if (count($this->references) < 1) {
            return null;
        }
        $returnArray = [];
        foreach ($this->references as $key => $value) {
            $returnArray[$key] = $this->serializeValue($value);
        }

        return $returnArray;
    }

    public function count(): int
    {
        return count($this->references);
    }

    public function current(): mixed
    {
        if (count($this->references) < 1) {
            return null;
        }

        $keys = array_keys($this->references);
        if (!isset($keys[$this->position])) {
            return null;
        }

        return $this->references[$keys[$this->position]];
    }

    public function key(): int|string
    {
        $keys = array_keys($this->references);

        return $keys[$this->position];
    }

    public function valid(): bool
    {

        $keys = array_keys($this->references);
        if (!isset($keys[$this->position])) {
            return false;
        }

        return isset($this->references[$keys[$this->position]]);
    }

}