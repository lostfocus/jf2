<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Property;

use JsonException;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\PropertyInterface;
use Lostfocus\Jf2\Property;
use stdClass;

class Content extends Property
{
    /**
     * @var array<string, string>
     */
    private array $contentProperties = [];

    public static function fromClass(stdClass $value): PropertyInterface
    {
        try {
            return self::fromArray(
                json_decode(json_encode($value, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR)
            );
        } catch (JsonException $e) {
            throw new Jf2Exception($e->getMessage(), Jf2Exception::JSON_EXCEPTION, $e);
        }
    }

    public static function fromArray(array $value): PropertyInterface
    {
        $content = new self();
        $content->contentProperties = $value;
        return $content;
    }


    /**
     * @return string[]
     */
    public function jsonSerialize(): array
    {
        return $this->contentProperties;
    }

    public function getHtml(): ?string
    {
        return $this->contentProperties['html'] ?? null;
    }

    public function getText(): ?string
    {
        return $this->contentProperties['text'] ?? null;
    }
}