---
layout: code
name: basics
---

# Content Negotiation Basics

This is a high-level overview of the basic concepts behind content negotiation.

## Variants

Variants are the different ways that the underlying information behind a resource (URI) can be encoded.

Variants of the same resource may vary by language, character encoding, mime type or encoding; the important concept is that these variations do not affect the content of the resource but merely it's representation.

## Quality Factors

Quality factors are used to indicate preferences for particular variant of a resource, they range from 1 for the ideal representation to 0 for a completely degraded representation of the resource. [RFC2295 Sec 5.3](http://tools.ietf.org/html/rfc2295#section-5.3) provides a guide to assigning quality factors:

~~~ plain
    1.000  perfect representation
    0.900  threshold of noticeable loss of quality
    0.800  noticeable, but acceptable quality reduction
    0.500  barely acceptable quality
    0.300  severely degraded quality
    0.000  completely degraded quality
~~~

## Type Preference Encodings

Types are encoded comma-separated and consist of the type alongside a quality factor that indicates our application's relative preference for that type. Higher quality factors are more favoured, but note that the absence of an explicit quality factor is equivalent to setting it to one.

This means that ```application/json``` has a higher preference than ```application/xml;q=0.8``` as the quality factor for the former is implicitly 1.

The resultant application preference string for negotiation in our API is ```application/json,application/xml;q=0.8``` (preferences may be provided in any order).
