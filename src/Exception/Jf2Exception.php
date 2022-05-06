<?php
declare(strict_types=1);

namespace Lostfocus\Jf2\Exception;

use Exception;

class Jf2Exception extends Exception
{
    public const JSON_EXCEPTION = 1;
    public const TYPE_MUST_BE_STRING = 2;
    public const UNABLE_TO_CAST_TO_STRING = 3;
    public const CONTENT_MUST_BE_STRING_OR_STDCLASS = 4;
    public const CONTENT_MUST_HAVE_HTML = 5;
    public const VIDEO_MUST_HAVE_URL = 6;
    public const UNEXPECTED_TYPE = 7;
}