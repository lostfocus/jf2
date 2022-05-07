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
    public const CLASS_SHOULD_HAVE_A_TYPE = 8;
    public const UNABLE_TO_SERIALIZE_VALUE = 9;
    public const CHILDREN_MUST_BE_ARRAY = 10;
    public const PROPERTY_SHOULD_BE_CLASS = 11;
    public const COLLECTION_SHOULD_ONLY_HAVE_OBJECTS = 12;
}