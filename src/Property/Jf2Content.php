<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Property;

use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\Jf2PropertyInterface;
use Lostfocus\Jf2\Jf2Property;
use stdClass;

class Jf2Content extends Jf2Property
{
    /**
     * @var array<string, string>
     */
    private array $contentProperties = [];

    /**
     * @throws Jf2Exception
     */
    public static function fromValue($value): Jf2PropertyInterface
    {
        if (is_string($value)) {
            return self::fromString($value);
        }

        if ($value instanceof stdClass) {
            $content = new self();

            $valueVars = get_object_vars($value);
            foreach ($valueVars as $key => $valueVar) {
                $content->contentProperties[$key] = $valueVar;
            }
            return $content;
        }

        throw new Jf2Exception(
            'Content MUST be a single string or an stdClass.',
            Jf2Exception::CONTENT_MUST_BE_STRING_OR_STDCLASS
        );
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