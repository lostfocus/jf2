<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use JsonSerializable;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\Jf2Interface;
use Lostfocus\Jf2\Interfaces\Jf2PropertyInterface;
use Lostfocus\Jf2\Property\Jf2Object;
use stdClass;

class Jf2Property implements Jf2PropertyInterface
{
    protected array $value = [];

    public static function fromString(string $value): Jf2PropertyInterface
    {
        return (new self())
            ->addValue($value);
    }

    /**
     * @throws Jf2Exception
     */
    public static function fromArray(array $value): Jf2PropertyInterface
    {
        $property = new self();
        foreach ($value as $item) {
            if ($item instanceof stdClass) {
                $property->addValue(self::fromClass($item));

            } else {
                $property->addValue($item);
            }
        }
        return $property;
    }

    /**
     * @throws Jf2Exception
     */
    public static function fromClass(stdClass $value): Jf2PropertyInterface
    {
        if (!property_exists($value, 'type')) {
            throw new Jf2Exception('Class should have a type', Jf2Exception::CLASS_SHOULD_HAVE_A_TYPE);
        }
        $property = new Jf2Object();
        $property->addValue(Jf2::fromJsonClass($value));
        return $property;
    }

    public static function fromJf2(Jf2Interface $value): Jf2PropertyInterface
    {
        $property = new self();
        $property->addValue($value);
        return $property;
    }

    /**
     * @return mixed
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

    /**
     * @param array|string|Jf2|Jf2PropertyInterface|null $value
     * @return $this
     */
    public function addValue(array|string|Jf2|Jf2PropertyInterface|null $value): Jf2PropertyInterface
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
     * @return array|string|Jf2|Jf2PropertyInterface|null
     */
    public function getValue(): array|string|Jf2|Jf2PropertyInterface|null
    {
        if (count($this->value) < 1) {
            return null;
        }
        if (count($this->value) > 1) {
            return $this->value;
        }
        return $this->value[0];
    }

    /**
     * @param mixed $value
     * @return float|int|mixed|string|null
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