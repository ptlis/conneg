ConNeg
======

A Content Negotiation library for PHP >= 5.3. The API provides support for negotiation on the  [Accept](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1), [Accept-Charset](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.2), [Accept-Encoding](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3) and [Accept-Language](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4) fields in a HTTP header.

[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)](https://travis-ci.org/ptlis/conneg) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/conneg/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/conneg/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/conneg/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/conneg/) [![Latest Stable Version](https://poser.pugx.org/ptlis/conneg/v/stable.png)](https://packagist.org/packages/ptlis/conneg)

TODO:
====

Check for invalid quality factors (not float, < 0 or > 1.
qFactorFactory - no "new QualityFactor" outside of here
AbstractFactory for Encoding, Language & Charset ? possibly concrete class & decorate with type specific
Wildcard types should not be allowed in provided application types
Out of order adding?
Re-order app/user type in TypePair constructor (consistancy)
TypePairs should not have null values in constructor
best -> perferred
TypePairInterface implements TypeInterface??
