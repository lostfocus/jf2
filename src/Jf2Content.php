<?php
declare(strict_types=1);

namespace Lostfocus\Jf2;

use Lostfocus\Jf2\Exception\Jf2Exception;
use stdClass;

class Jf2Content extends Jf2Property
{
    /**
     * @throws Jf2Exception
     */
    public static function fromValue($value): Jf2PropertyInterface
    {
        if (is_string($value)) {
            return self::fromString($value);
        }

        $content = new self();

        if ($value instanceof stdClass) {
            if (!property_exists($value, 'html')) {
                throw new Jf2Exception(
                    'Content MUST have an HTML property.',
                    Jf2Exception::CONTENT_MUST_HAVE_HTML
                );
            }

            foreach ($value as $key => $item) {
                $content->addValueWithKey($key, $item);
            }
            return $content;
        }
        throw new Jf2Exception(
            'Content MUST be a single string or an stdClass.',
            Jf2Exception::CONTENT_MUST_BE_STRING_OR_STDCLASS
        );
    }

    private function addValueWithKey(string $key, string $item): void
    {
        $this->value[$key] = $item;
    }

    public function getHtml(): ?string
    {
        return $this->value['html'] ?? null;
    }

    public function getText(): ?string
    {
        return $this->value['text'] ?? null;
    }
}