<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Property;

use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\Jf2PropertyInterface;
use Lostfocus\Jf2\Jf2Property;
use stdClass;

class Jf2Media extends Jf2Property
{
    /**
     * @var array<string, string>
     */
    private array $mediaProperties = [];

    /**
     * @param array|string|stdClass $value
     * @return Jf2PropertyInterface
     * @throws Jf2Exception
     */
    public static function fromValue(array|string|stdClass $value): Jf2PropertyInterface
    {
        if (is_string($value)) {
            return self::fromString($value);
        }

        if (is_array($value)) {
            $property = new Jf2Property();
            foreach ($value as $item) {
                $property->addValue(self::fromValue($item));
            }
            return $property;
        }

        if ($value instanceof stdClass) {
            $media = new self();
            $valueVars = get_object_vars($value);
            foreach ($valueVars as $key => $valueVar) {
                $media->mediaProperties[$key] = $valueVar;
            }
            return $media;
        }
        throw new Jf2Exception(
            'Unexpected type: ' . gettype($value),
            Jf2Exception::UNEXPECTED_TYPE
        );
    }

    /**
     * @return string[]
     */
    public function jsonSerialize(): array
    {
        return $this->mediaProperties;
    }
}