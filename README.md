# ConNeg

A Content Negotiation library for PHP >= 5.3. The API provides support for negotiation on the  [Accept](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1), [Accept-Charset](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.2), [Accept-Encoding](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3) and [Accept-Language](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4) fields in a HTTP header.

[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)](https://travis-ci.org/ptlis/conneg) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/conneg/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/conneg/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/conneg/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/conneg/) [![Latest Stable Version](https://poser.pugx.org/ptlis/conneg/v/stable.png)](https://packagist.org/packages/ptlis/conneg)

## Install

Either from the console:

```shell
    $ composer require ptlis/conneg:~3.0.0
```

Or by Editing composer.json:

```javascript
    {
        "require": {
            ...
            "ptlis/conneg": "~3.0.0",
            ...
        }
    }
```

Followed by a composer update:

```shell
    $ composer update
```

Use negotiator:

```php
    use ptlis\ConNeg\Negotiate;
```

## Documentation

[Full Documentation](http://ptlis.github.io/conneg/)

## Integration

* Symfony2 via the [ptlis/conneg-bundle](https://github.com/ptlis/conneg-bundle) Bundle.

## Contributing

You can contribute by submitting an Issue to the [issue tracker](https://github.com/ptlis/conneg/issues), improving the [documentation](https://github.com/ptlis/conneg/tree/gh-pages), integrating the library into your framework of choice or submitting a pull request. For pull requests i'd prefer that the code style and test coverage is maintained, but I am happy to work through any minor issues that may arise so that the request can be merged.


## TODO

* Handle accept-extens
* Time based negotiation? See RFC 7089
