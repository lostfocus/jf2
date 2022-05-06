<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use Countable;
use JsonSerializable;
use stdClass;
use Stringable;

interface Jf2PropertyInterface extends  JsonSerializable, Stringable, Countable
{
    public static function fromString(string $value): self;

    public static function fromArray(array $value): self;

    public static function fromClass(stdClass $value): self;

    public function addValue(array|string|Jf2|null $value): self;

    public function getValue(): array|string|Jf2|null;
}