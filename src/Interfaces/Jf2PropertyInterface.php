<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Interfaces;

use Countable;
use JsonSerializable;
use Lostfocus\Jf2\Jf2;
use stdClass;
use Stringable;

interface Jf2PropertyInterface extends  JsonSerializable, Stringable, Countable
{
    public static function fromString(string $value): self;

    public static function fromArray(array $value): self;

    public static function fromClass(stdClass $value): self;

    public function addValue(array|string|Jf2|Jf2PropertyInterface|null $value): self;

    public function getValue(): array|string|Jf2|Jf2PropertyInterface|null;
}