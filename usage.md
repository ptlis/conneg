---
layout: code
name: usage
---

# Usage

Create a Negotiation instance:

~~~ php
use ptlis\ConNeg\Negotiation;

$negotiation = new Negotiation();
~~~


## Preferred Variant

In most cases you will only care about the 'best' match between variants that your application serves and those the client accepts. In this case use the ```Negotiation::*Best``` methods:
  
* ```Negotiation::charsetBest()```
* ```Negotiation::encodingBest()```
* ```Negotiation::languageBest()```
* ```Negotiation::mimeBest()```

These methods (along with the ```*All()``` complimentary methods) accept two parameters. The first is the contents of the appropriate header field (e.g. ```Accept``` for mime negotiation) and the second is the server's preferences: 

~~~ php
$mimeBest = $negotiator->mimeBest(
    $_SERVER['HTTP_ACCEPT'],
    $serverPrefs
);
~~~






And say that the client provided an Accept field like this:

~~~ plain
Accept: text/html;q=0.1,application/xml;q=0.75,text/plain;q=0.2,application/json;q=0.9
~~~

### Preferred Type Example

To simply get the preferred mime type use the ```mimeBest``` method which requires the contents of the client's Accept field as well as your application's preferences:

~~~ php
$mimeBest = $negotiator->mimeBest(
    $_SERVER['HTTP_ACCEPT'],
    'application/json,application/xml;q=0.8,text/n3;q=0.5'
);
~~~

```$mimeBest``` is an instance of ```ptlis\ConNeg\TypePair\MimeTypePair```, providing (amongst others) the following methods:

~~~ php
echo 'Encoded:                ' . $mimeBest . PHP_EOL;
echo 'Type:                   ' . $mimeBest->getType() . PHP_EOL;
echo 'Quality Factor Product: ' . $mimeBest->getQualityFactor() . PHP_EOL;
echo 'Quality Factor UA:      ' . $mimeBest->getUserType()->getQualityFactor() . PHP_EOL;
echo 'Quality Factor App      ' . $mimeBest->getAppType()->getQualityFactor() . PHP_EOL;

/* Output:
    Encoded:                application/json;q=0.9
    Type:                   application/json
    Quality Factor Product: 0.9
    Quality Factor UA:      0.9
    Quality Factor App      1
*/
~~~

### All Types Example

To get a collection containing all processed types use the ```mimeAll``` method which takes the same parameters as ```mimeBest```:

~~~ php
$mimeAllList = $negotiator->mimeAll(
    $_SERVER['HTTP_ACCEPT'],
    'application/json,application/xml;q=0.8,text/n3;q=0.5'
);
~~~

```$mimeAllList``` is an instance of ```ptlis\ConNeg\Collection\MimeTypePairCollection``` containing the found types which may be used in your application code. Some examples:

~~~ php
foreach ($mimeAllList as $mimeType) {
    echo $mimeType . PHP_EOL;
}

/* Output:
    application/json;q=0.9
    application/xml;q=0.6
    text/plain;q=0
    text/html;q=0
    text/n3;q=0
*/

echo $mimeAllList . PHP_EOL;

/* Output:
    application/json;q=0.9,application/xml;q=0.6,text/plain;q=0,text/html;q=0,text/n3;q=0
*/

$ascMimeAllList = $mimeAllList->getAscending();
echo $ascMimeAllList . PHP_EOL;

/* Output:
    text/n3;q=0,text/html;q=0,text/plain;q=0,application/xml;q=0.6,application/json;q=0.9
*/

echo $ascMimeAllList->getBest() . PHP_EOL;

/* Output:
    application/json;q=0.9
*/
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
