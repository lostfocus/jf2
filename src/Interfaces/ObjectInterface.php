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

    /**
     * @param  string  $key
     * @param  string|array<mixed>|stdClass  $value
     * @return $this
     */
    public function add(string $key, string|array|stdClass $value): self;

    /**
     * @param  string  $key
     * @param  string|array<mixed>|stdClass  $value
     * @return $this
     */
    public function replace(string $key, string|array|stdClass $value): self;

    public function get(string $key): mixed;

    public function has(string $key): bool;

    public function remove(string $key): self;

    public function addProperty(string $key, PropertyInterface $value): self;

    public function replaceProperty(string $key, PropertyInterface $value): self;

    public function getProperty(string $key): ?PropertyInterface;

    public function hasProperty(string $key): bool;

    public function removeProperty(string $key): self;

    public function addChild(ObjectInterface $value): self;

    public function getChildren(): ?PropertyInterface;

    public function getType(): ?string;

    public function getReference(string $key): ?ObjectInterface;

    /**
     * @return array<string, PropertyInterface>
     */
    public function getProperties(): array;
}