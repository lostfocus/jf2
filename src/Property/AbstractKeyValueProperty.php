<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Property;

use Lostfocus\Jf2\Jf2Property;

abstract class AbstractKeyValueProperty extends Jf2Property
{
    protected function addValueWithKey(string $key, string $item): void
    {
        $this->value[$key] = $item;
    }

}