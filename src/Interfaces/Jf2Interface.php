<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Interfaces;

use Countable;
use JsonSerializable;
use Lostfocus\Jf2\Exception\Jf2Exception;
use stdClass;
use Stringable;

interface Jf2Interface extends JsonSerializable, Stringable, Countable
{
    /**
     * @param string $jsonString
     * @return Jf2Interface
     * @throws Jf2Exception
     */
    public static function fromJsonString(string $jsonString): self;

    /**
     * @param stdClass $json The result of a json_decode
     * @return Jf2Interface
     * @throws Jf2Exception
     */
    public static function fromJsonClass(stdClass $json): self;

    /**
     * @param string $key
     * @param array|float|int|string|stdClass|self $value
     * @return Jf2Interface
     * @throws Jf2Exception
     */
    public function addProperty(string $key, array|float|int|string|stdClass|self $value): self;
    public function getProperty(string $key): ?Jf2PropertyInterface;
    public function hasProperty(string $key): bool;

}