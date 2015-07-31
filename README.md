# ConNeg

Content Negotiation for PHP.
 
This framework-independent library provides tooling to allow you to support content negotiation in your applications.

Supports negotiation on the  [Accept](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1), [Accept-Charset](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.2), [Accept-Encoding](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3) and [Accept-Language](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4) fields in a HTTP header.

[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)](https://travis-ci.org/ptlis/conneg) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/conneg/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/conneg/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/conneg/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/conneg/)  [![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/ptlis/conneg/blob/master/LICENSE) [![Latest Stable Version](https://poser.pugx.org/ptlis/conneg/v/stable.png)](https://packagist.org/packages/ptlis/conneg)

## Install

Either from the console:

```shell
$ composer require ptlis/conneg:~4.0.0.alpha.1
```

Or by manually editing your composer.json:

```javascript
{
    "require": {
        "ptlis/conneg": "~4.0.0.alpha.1"
    }
}
```

Followed by a composer update:

```shell
$ composer update
```

## Usage


### In a PSR-7 Complaint Project

If your application supports PSR-7 then the simplest way to get content negotiation is via the middlewares provided by [ptlis/psr7-conneg](https://github.com/ptlis/psr7-conneg).


### In non PSR-7 Projects & Advanced Use-Cases

Create a negotiator instance:

```php
use ptlis\ConNeg\Negotiation;

$negotiation = new Negotiation();
```

The Negotiation instance we've created here provides methods to negotiate on preferences provided by the client and application.

Methods are available for negotiation on mime types, languages, charsets and encodings ([Accept](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1), [Accept-Language](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4), [Accept-Charset](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.2) and [Accept-Encoding](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3) HTTP fields respectively) 

In most cases your application will only care about the computed best match, in which case use the best* methods:

```php
$bestMime     = $negotiation->mimeBest($_SERVER['ACCEPT'], $appMimePrefs);
$bestLanguage = $negotiation->languageBest($_SERVER['ACCEPT_LANGUAGE'], $appLangPrefs);
$bestCharset  = $negotiation->charsetBest($_SERVER['ACCEPT_CHARSET'], $appCharsetPrefs);
$bestEncoding = $negotiation->encodingBest($_SERVER['ACCEPT_ENCODING'], $appEncPrefs);
```

These will return objects implementing MatchedPreferencesInterface - in most cases you will only want the calculated type:

```php
$mime = $bestMime->getType();
// E.g. $mime === 'text/html'
```

In more advanced cases you may need the metadata associated with the type:

```php
$qualityFactor = $mime->getQualityFactor(); // Product of the client & server preferences
// E.g. $qualityFactor === 0.75;

$qualityFactor = $mime->getPrecedence(); //Sum of client & server precedences
// E.g. $qualityFactor === 3;

// Returns an object implementing PreferenceInterface that represents the client's
// preference. You may then call the getQualityFactor() and getPrecedence() on this
// instance
$clientPref = $mime->getUserPreference();

// As above but for the server's preference
$serverPref = $mime->getAppPreference();
```




## Documentation

[Full Documentation](http://ptlis.github.io/conneg/)

## Integration

* PSR-7 via the [ptlis/psr7-conneg](https://github.com/ptlis/psr7-conneg) package, with middlewares supporting:
    * [Zend Stratigility](https://github.com/zendframework/zend-stratigility)
    * [Relay](https://github.com/relayphp/Relay.Relay)
* Symfony2 via the [ptlis/conneg-bundle](https://github.com/ptlis/conneg-bundle) Bundle.

## Contributing

You can contribute by submitting an Issue to the [issue tracker](https://github.com/ptlis/conneg/issues), improving the [documentation](https://github.com/ptlis/conneg/tree/gh-pages), integrating the library into your framework of choice or submitting a pull request. For pull requests i'd prefer that the code style and test coverage is maintained, but I am happy to work through any minor issues that may arise so that the request can be merged.


## TODO

* Time based negotiation? See RFC 7089
