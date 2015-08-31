---
layout: code
name: usage
---

# Usage

* Will be replaced with the ToC
{:toc}

Create a Negotiation instance:

~~~ php
use ptlis\ConNeg\Negotiation;

$negotiation = new Negotiation();
~~~


## Preferred Variant

In most cases you will only care about the 'best' matching variant. The ```Negotiation::*Best``` methods provide this:
  
* ```Negotiation::charsetBest()```
* ```Negotiation::encodingBest()```
* ```Negotiation::languageBest()```
* ```Negotiation::mimeBest()```

These methods accept two parameters. The first is the contents of the appropriate header field (e.g. ```Accept``` for mime negotiation) and the [string encoding of server preferences](http://localhost:4000/basics.html#preference-encoding): 

~~~ php
$mimeBest = $negotiator->mimeBest(
    $clientPrefs,
    $serverPrefs
);
~~~

Fox example, given the client's preferences:

~~~ php
$clientPrefs = 'text/html;q=0.1,application/xml;q=0.75,text/plain;q=0.2,application/json;q=0.9';
~~~

And the server preferences:

~~~ plain
$serverPrefs = 'application/json,application/xml;q=0.8,text/n3;q=0.5';
~~~


Then ```$mimeBest``` would contain ```application/json```.



## All Matching Variants

To get an array of containing all matched variants use the ```*All```methods:

* ```Negotiation::charsetAll()```
* ```Negotiation::encodingAll()```
* ```Negotiation::languageAll()```
* ```Negotiation::mimeAll()```

As with the ```*Best``` methods, these accept two parameters. The first is the contents of the appropriate header field (e.g. ```Accept``` for mime negotiation) and the [string encoding of server preferences](http://localhost:4000/basics.html#preference-encoding)

~~~ php
$mimeAllList = $negotiator->mimeAll(
    $clientPrefs,
    $serverPrefs
);
~~~

```$mimeAllList``` an array of objects implementing [```MatchedPreferenceInterface```](https://github.com/ptlis/conneg/blob/master/src/Preference/Matched/MatchedPreferenceInterface.php), sorted by preference (descending).

From these we can read the variant data provided by the client & server:

~~~ php

$preferredVariant = $mimeAllList[0];

$variantName = $preferredVariant->getVariant();
// $variantName === 'application/json';

$qualityFactor = $preferredVariant->getQualityFactor();
// $qualityFactor === 1;

$clientPref = $bestType->getClientPreference();
// Preference object for client variant & quality factor

$serverPref = $bestType->getServerPreference();
// Preference object for server variant & quality factor
~~~


## Negotiation Strategies

There are three strategies that can be used when building an application that supports content negotiation; Server-Driven ([RFC 2616 Sec. 12.1](http://tools.ietf.org/html/rfc2616#section-12.1)), Agent-Driven ([RFC 2616 Sec. 12.2](tools.ietf.org/html/rfc2616#section-12.2)) and Transparent ([RFC 2295](http://tools.ietf.org/html/rfc2295)).

### Server-Driven Negotiation

Your application must do one of two things when server-driven negotiation is used; when the application has one URI for each representation of a resource you must perform a 303 Redirect to that URI, setting the Vary field. For example, if negotiation is performed on the Accept-Language field then the response would look like:

~~~ php
header('Vary: Accept-Language');
header('HTTP/1.1 303 See Other');
header('Location: /path/to/resource/');
~~~

When your application serves all representations of the resource from the same URI then you must set the Vary field (as well as changing the fields indicating the type of resource returned). For example, if negotiation has been performed on the Accept and Accept Charset field the response would look something like:

~~~ php
header('Vary: Accept, Accept-Charset');
header('Content-Type: ' . $mimeBest . '; charset=' . $charsetBest);
~~~
