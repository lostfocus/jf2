<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Property;

use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Property;

class Item extends Property
{
    /**
     * @return int
     * @throws Jf2Exception
     */
    public function count(): int
    {
        if(count($this->value) !== 1) {
            throw new Jf2Exception('Unexpected item type', Jf2Exception::UNEXPECTED_TYPE);
        }
        return count($this->value[0]);
    }
}