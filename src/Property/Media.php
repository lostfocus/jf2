<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Property;

use Lostfocus\Jf2\Interfaces\PropertyInterface;
use Lostfocus\Jf2\Property;
use stdClass;

class Media extends Property
{
    /**
     * @var array<string, string>
     */
    private array $mediaProperties = [];

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


}