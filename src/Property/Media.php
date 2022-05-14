<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Property;

use JsonException;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\PropertyInterface;
use Lostfocus\Jf2\Property;
use stdClass;

class Media extends Property
{
    /**
     * @var array<string, string>
     */
    private array $mediaProperties = [];

    /**
     * @param  array<string, mixed> $value
     * @return PropertyInterface
     * @throws Jf2Exception
     */
    public static function fromArray(array $value): PropertyInterface
    {
        try {
            return self::fromClass(
                json_decode(json_encode($value, JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR)
            );
        } catch (JsonException $e) {
            throw new Jf2Exception($e->getMessage(), Jf2Exception::JSON_EXCEPTION, $e);
        }
    }

    public static function fromClass(stdClass $value): PropertyInterface
    {
        $media = new self();
        if (property_exists($value, 'content-type')) {
            $media->mediaProperties['content-type'] = $value->{'content-type'};
        }
        if (property_exists($value, 'url')) {
            $media->mediaProperties['url'] = $value->url;
        }

        return $media;
    }

    /**
     * @return string[]
     */
    public function jsonSerialize(): array
    {
        return $this->mediaProperties;
    }

    public function getContentType(): ?string
    {
        return $this->mediaProperties['content-type'] ?? null;
    }

    public function getUrl(): ?string
    {
        return $this->mediaProperties['url'] ?? null;
    }

}