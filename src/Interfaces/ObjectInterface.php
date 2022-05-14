<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Interfaces;

use Countable;
use Iterator;
use JsonSerializable;
use stdClass;
use Stringable;

/**
 * @extends Iterator<string, PropertyInterface>
 */
interface ObjectInterface extends JsonSerializable, Stringable, Countable, Iterator
{
    public static function fromString(string $value): self;

    /**
     * @param  array<mixed>  $value
     * @return static
     */
    public static function fromArray(array $value): self;
    public static function fromClass(stdClass $value): self;

    /**
     * @param  string|array<mixed>|stdClass  $value
     * @return static
     */
    public static function fromValue(string|array|stdClass $value): self;

    public function addProperty(string $key, PropertyInterface $value): self;
    public function replaceProperty(string $key, PropertyInterface $value): self;
    public function getProperty(string $key): ?PropertyInterface;
    public function hasProperty(string $key): bool;
    public function removeProperty(string $key): self;

    public function addChild(ObjectInterface $value): self;

    public function getChildren(): ?PropertyInterface;

    public function getType(): ?string;

    /**
     * @return array<string, PropertyInterface>
     */
    public function getProperties(): array;
}