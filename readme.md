# lostfocus/jf2

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)

A small-ish collection of classes to map/serialize JF2-Json files to typed objects
and back.

[More infos on JF2](https://www.w3.org/TR/jf2/)

[Test JF2 files taken from here.](https://github.com/dissolve/jf2_validator)

## Install

Via Composer

``` bash
$ composer require lostfocus/jf2
```

## Usage

``` php
$entry = new Lostfocus\Jf2\Utility\Entry();
$entry->setUrl('https://example.com');
echo json_encode($entry, JSON_PRETTY_PRINT |JSON_THROW_ON_ERROR);
```

## Testing

``` bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/lostfocus/jf2.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg

[link-packagist]: https://packagist.org/packages/lostfocus/jf2