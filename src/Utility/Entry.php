<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Utility;

use DateTime;
use DateTimeInterface;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\PropertyInterface;
use Lostfocus\Jf2\Item;
use Lostfocus\Jf2\Property;

class Entry extends Item
{
    /**
     * @throws Jf2Exception
     */
    public function __construct()
    {
        $this->properties['type'] = Property::fromString('entry');
    }

    /**
     * @throws Jf2Exception
     */
    public function setPublished(DateTime $published): self
    {
        $this->properties['published'] = Property::fromString($published->format('c'));
        return $this;
    }

    public function getPublished(): DateTime|false|null
    {
        $published = $this->getProperty('published');
        if ($published === null) {
            return null;
        }
        return DateTime::createFromFormat(DateTimeInterface::ATOM, (string)$published);
    }

    /**
     * @throws Jf2Exception
     */
    public function setUrl(string $url): self
    {
        $this->properties['url'] = Property::fromString($url);
        return $this;
    }

    public function getUrl(): ?string
    {
        $url = $this->getProperty('url');
        return ($url instanceof PropertyInterface) ? (string)$url : null;
    }
}