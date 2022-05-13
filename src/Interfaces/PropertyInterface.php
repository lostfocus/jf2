<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Interfaces;

use Countable;
use Iterator;
use JsonSerializable;
use stdClass;
use Stringable;

interface PropertyInterface extends JsonSerializable, Stringable, Countable, Iterator
{
    public static function fromString(string $value): self;
    public static function fromArray(array $value): self;
    public static function fromClass(stdClass $value): self;
    public static function fromValue(string|array|stdClass|ObjectInterface $value): self;

    public function addValue(string|array|stdClass|ObjectInterface|PropertyInterface $value): self;
    public function replaceValue(string|array|stdClass|ObjectInterface|PropertyInterface $value): self;
    public function getValue(): string|array|stdClass|ObjectInterface|PropertyInterface|null;

    public function isString(): bool;
}