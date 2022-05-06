<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use Countable;
use JsonSerializable;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Stringable;

class Jf2Property implements JsonSerializable, Stringable, Countable
{
    private array $value = [];

    public static function fromString(string $value): self
    {
        return (new self())
            ->addValue($value);
    }

    /**
     * @throws Jf2Exception
     */
    public static function fromArray(array $value): self
    {
        $property = new self();
        if (array_key_exists('type', $value)) {
            $property->addValue(Jf2::fromJsonArray($value));
        } else {
            foreach ($value as $item) {
                $property->addValue($item);
            }
        }
        return $property;
    }

    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }

    /**
     * @param array|string|Jf2|null $value
     * @return $this
     */
    public function addValue(array|string|Jf2|null $value): self
    {
        if (!in_array($value, $this->value, true)) {
            $this->value[] = $value;
        }
        return $this;
    }

    /**
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

    public function count(): int
    {
        return count($this->value);
    }

    /**
     * @return array|string|Jf2|null
     */
    public function getValue(): array|string|Jf2|null
    {
        if (count($this->value) < 1) {
            return null;
        }
        if (count($this->value) > 1) {
            return $this->value;
        }
        return $this->value[0];
    }
}