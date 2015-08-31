---
layout: code
name: basics
---

# Concepts

This is a high-level overview of the terms and concepts underlying content negotiation.

## Variants

Variants are the different ways that the underlying information behind a resource (URI) can be encoded.

Variants of the same resource may vary by language, charset, mime type or encoding; the important concept is that these variations do not affect the content of the resource but merely it's representation.

For example, if the same image saved as PNG and JPEG would be variants for this purpose.


## Variant Matching

This is the process through which content negotiation occurs. There are several possible matching rules that can cause a server & client preference to be paired together. Each rule type has an associated precedence which is used to ensure only the most specific match is applied.

The precedence of the rules are as follows (highest to lowest):

* **Exact Match**
* **Mime Subtype/Partial Language Wildcard Match** (equivalent but mutually exclusive)
* **Full Wildcard Match**
* **No Match**


### Exact Match

Occurs when an exact match is found in client & server preferences.

**Note:** When doing mime negotiation accept-extens fragments are discarded - this means a client preference of ```text/html;level=4``` will be an exact match for the server preference ```text/html```.


### Mime Subtype Wildcard Match

Specified by clients in Accept fields, these match any subtype of the parent type.

For example ```text/*``` matches both ```text/html``` and ```text/plain``` but not ```application/json```.


### Partial Language Wildcard Match

Specified by the server when negotiating on the Accept-Language field, they allow the application developer to group language families as a 'last resort' for matching purposes.

Say your language supports Spanish (```es```) - it would be burdensome to have to specify every permutation of extlangs in your server preferences (e.g. ```es```, ```es-419```, ```es-CO```, ```es-ES```). 

Instead you can specify a family by using ```es-*``` which will match any language string beginning in ```es```. When using the ```languageBest()``` method the returned variant name will always be ```es``` rather than ```es-*``` or the client's full language tag as this aids for direct lookup.


### Full Wildcard Match

For all types of negotiation clients can specify catch-all variants with a wildcard (e.g. ```*``` or ```*/*```).

These match any server variant.


### No Match

Used in one side of a MatchedPreference instance when the variant was present only in the server or client preferences.


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
