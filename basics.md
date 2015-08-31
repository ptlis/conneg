---
layout: code
name: basics
---

# Content Negotiation Basics

This is a high-level overview of the terms and concepts underlying content negotiation.

## Variants

Variants are the different ways that the underlying information behind a resource (URI) can be encoded.

Variants of the same resource may vary by language, character encoding, mime type or encoding; the important concept is that these variations do not affect the content of the resource but merely it's representation.

For example, if the same image saved as PNG and JPEG would be variants for this purpose. 

## Quality Factors

Quality factors are used to describe variant preferences, having a range from 1 (for the ideal representation) to 0 (for a completely degraded representation of the resource). Thee steps between are used to indicate varying levels of preference.

[RFC2295 Sec 5.3](http://tools.ietf.org/html/rfc2295#section-5.3) provides a guide to assigning quality factors:

~~~markdown
1.000  perfect representation
0.900  threshold of noticeable loss of quality
0.800  noticeable, but acceptable quality reduction
0.500  barely acceptable quality
0.300  severely degraded quality
0.000  completely degraded quality
~~~

## Preference Encoding

Preferences are encoded in a comma-separated list. Each element of the list consists of a type (e.g. ```text/html```) and optionally with a semicolon and a quality factor (e.g ```q=0.5```)

This means that single preference looks like ```application/xml;q=0.7``` or ```application/json```, and in list form they would look like ```application/xml;q=0.7,application/json```.
