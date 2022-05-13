<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Property;

use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\ObjectInterface;
use Lostfocus\Jf2\Interfaces\PropertyInterface;
use Lostfocus\Jf2\Property;
use stdClass;

class Collection extends Property
{
    /**
     * @throws Jf2Exception
     */
    public function addValue(PropertyInterface|array|string|stdClass|ObjectInterface $value): PropertyInterface
    {
        if ($value instanceof PropertyInterface) {
            $addedValue = $value->getValue();
            if ($addedValue === null) {
                return $this;
            }
            $value = $addedValue;
        }

        if (!$value instanceof ObjectInterface) {
            throw new Jf2Exception(
                'Collection should only have objects',
                Jf2Exception::COLLECTION_SHOULD_ONLY_HAVE_OBJECTS
            );
        }

        return parent::addValue($value);
    }
}