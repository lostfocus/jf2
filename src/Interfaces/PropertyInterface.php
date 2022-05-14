<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Interfaces;

use Countable;
use Iterator;
use JsonSerializable;
use stdClass;
use Stringable;

/**
 * @extends Iterator<mixed>
 */
interface PropertyInterface extends JsonSerializable, Stringable, Countable, Iterator
{
    public static function fromString(string $value): self;

    /**
     * @param  array<mixed>  $value
     * @return static
     */
    public static function fromArray(array $value): self;
    public static function fromClass(stdClass $value): self;

    /**
     * @param  string|array<mixed>|stdClass|ObjectInterface  $value
     * @return static
     */
    public static function fromValue(string|array|stdClass|ObjectInterface $value): self;

    /**
     * @param  string|array<mixed>|stdClass|ObjectInterface|PropertyInterface  $value
     * @return $this
     */
    public function addValue(string|array|stdClass|ObjectInterface|PropertyInterface $value): self;

    /**
     * @param  string|array<mixed>|stdClass|ObjectInterface|PropertyInterface  $value
     * @return $this
     */
    public function replaceValue(string|array|stdClass|ObjectInterface|PropertyInterface $value): self;

    /**
     * @return string|array<mixed>|stdClass|ObjectInterface|PropertyInterface|null
     */
    public function getValue(): string|array|stdClass|ObjectInterface|PropertyInterface|null;

    public function isString(): bool;
}