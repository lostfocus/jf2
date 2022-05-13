<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Property;

use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\PropertyInterface;
use Lostfocus\Jf2\Item;
use Lostfocus\Jf2\Property;
use stdClass;

class Jf2References extends Property
{
    /** @var PropertyInterface[] */
    private array $references = [];

    public static function fromClass(stdClass $value): PropertyInterface
    {
        $property = new self();
        $objectVars = get_object_vars($value);
        foreach ($objectVars as $key => $objectValue) {
            $property->references[$key] = parent::fromClass($objectValue);
        }
        return $property;
    }

    public function count(): int
    {
        return count($this->references);
    }

    public function jsonSerialize(): array
    {
        $return = [];
        foreach ($this->references as $key => $value) {
            $return[$key] = $value->jsonSerialize();
        }
        return $return;
    }

    /**
     * @param $key
     * @return Object|null
     * @throws Jf2Exception
     */
    public function getReference($key): ?Item
    {
        if (!array_key_exists($key, $this->references)) {
            return null;
        }
        if (!$this->references[$key] instanceof Jf2Object) {
            throw new Jf2Exception(
                'References MUST be an object',
                Jf2Exception::REFERENCES_MUST_BE_OBJECT
            );
        }
        return $this->references[$key]->getValue();
    }
}