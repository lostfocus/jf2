<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Utility;

use DateTime;
use DateTimeInterface;
use Lostfocus\Jf2\Exception\Jf2Exception;
use Lostfocus\Jf2\Interfaces\Jf2PropertyInterface;
use Lostfocus\Jf2\Jf2;

class Entry extends Jf2
{
    /**
     * @throws Jf2Exception
     */
    public function __construct()
    {
        $this->addProperty('type', 'entry');
    }

    /**
     * @throws Jf2Exception
     */
    public function setPublished(DateTime $published): self
    {
        $this->addProperty('published', $published->format('c'));
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

    /**
     * @param string $url
     * @return $this
     * @throws Jf2Exception
     */
    public function setUrl(string $url): self
    {
        $this->addProperty('url', $url);
        return $this;
    }

    public function getUrl(): ?string
    {
        $url = $this->getProperty('url');
        return ($url instanceof Jf2PropertyInterface) ? (string)$url->getValue() : null;
    }
}