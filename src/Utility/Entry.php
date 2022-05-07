<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Utility;

use DateTime;
use DateTimeInterface;
use Lostfocus\Jf2\Interfaces\Jf2PropertyInterface;
use Lostfocus\Jf2\Jf2;
use Lostfocus\Jf2\Jf2Property;

class Entry extends Jf2
{
    public function __construct()
    {
        $this->properties['type'] = Jf2Property::fromString('entry');
    }

    public function setPublished(DateTime $published): self
    {
        $this->properties['published'] = Jf2Property::fromString($published->format('c'));
        return $this;
    }

    public function getPublished(): ?DateTime
    {
        $published = $this->getProperty('published');
        if ($published === null) {
            return null;
        }
        return DateTime::createFromFormat(DateTimeInterface::ATOM, (string)$published);
    }

    public function setUrl(string $url): self
    {
        $this->properties['url'] = Jf2Property::fromString($url);
        return $this;
    }

    public function getUrl(): ?string
    {
        $url = $this->getProperty('url');
        return ($url instanceof Jf2PropertyInterface) ? (string)$url->getValue() : null;
    }
}