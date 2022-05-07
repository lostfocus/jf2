<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Property;

use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\Jf2PropertyInterface;
use stdClass;

class Jf2Media extends AbstractKeyValueProperty
{

    /**
     * @throws Jf2Exception
     */
    public static function fromValue($value): Jf2PropertyInterface
    {
        if (is_string($value)) {
            return self::fromString($value);
        }
        if ($value instanceof stdClass) {
            if (!property_exists($value, 'url')) {
                throw new Jf2Exception(
                    'Video MUST have an URL property.',
                    Jf2Exception::VIDEO_MUST_HAVE_URL
                );
            }
            $video = new self();

            foreach ($value as $key => $item) {
                $video->addValueWithKey($key, $item);
            }
            return $video;
        }
        if (is_array($value)) {
            $videos = new self();
            foreach ($value as $item) {
                $videos->addValue(self::fromValue($item));
            }
            return $videos;
        }
        throw new Jf2Exception(
            'Unexpected type: ' . gettype($value),
            Jf2Exception::UNEXPECTED_TYPE
        );

    }
}