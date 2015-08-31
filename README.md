# ConNeg

Content Negotiation for PHP.

This framework-independent library provides tooling to allow you to support content negotiation in your applications.

Supports negotiation on the  [Accept](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1), [Accept-Charset](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.2), [Accept-Encoding](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3) and [Accept-Language](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4) fields in a HTTP header.

[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)](https://travis-ci.org/ptlis/conneg) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/conneg/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/conneg/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/conneg/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/conneg/)  [![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/ptlis/conneg/blob/master/LICENSE) [![Latest Stable Version](https://poser.pugx.org/ptlis/conneg/v/stable.png)](https://packagist.org/packages/ptlis/conneg)

## Install

With composer:

```shell
$ composer require ptlis/conneg:~4.0.0
```

## Usage


### In a PSR-7 Project

If your application supports PSR-7 then the simplest way to get content negotiation is via the middlewares provided by [ptlis/psr7-conneg](https://github.com/ptlis/psr7-conneg).


### In non PSR-7 Projects

First create a Negotiation instance. This provides methods to perform negotiation on client and server preferences.

```php
use ptlis\ConNeg\Negotiation;

$negotiation = new Negotiation();
```

In most cases your application will only care about the best match, to get these we can use the ```*Best()``` methods.

For example, negotiation to decide whether to serve JSON or XML (preferring JSON) would look like:

```php
$bestMime = $negotiation->mimeBest(
    $_SERVER['ACCEPT'], 
    'application/json;q=1,application/xml;q=0.75'
);
```

This will return a string representation of the best matching mime-type specified by the server's preferences, for example 'application/json'.

Negotiation of Language, Encoding & Charset can be done by using the appropriate method (languageBest, encodingBest & charsetBest respectively).

**Note:** server preferences a string-encoded as described [in the documentation](http://ptlis.github.io/conneg/basics.html#type-preference-encodings).

See the [detailed usage docs](http://ptlis.github.io/conneg/usage.html) for further (more complex) examples.




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
