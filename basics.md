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

Preferences should be encoded as described in [RFC2126](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html).

In brief this is a comma-separated list, each element consisting of a type (e.g. ```text/html```) and optional quality factor (e.g ```text/html;q=0.5```).

For example, given the following preferences:

<table class="table table-striped">
    <thead>
        <tr>
            <th>Mime Type</th>
            <th>Relative Preference</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>application/json</td>
            <td>Best representation available</td>
        </tr>
        <tr>
            <td>application/xml</td>
            <td>Less than ideal; our data doesn't encode well in XML</td>
        </tr>
        <tr>
            <td>text/html</td>
            <td>Allow fallback for humans, but not useful for other applications</td>
        </tr>
    </tbody>
</table>

We can assign appropriate quality factors to each variant:


<table class="table table-striped">
    <thead>
        <tr>
            <th>Mime Type</th>
            <th>Quality Factor</th>
            <th>Encoded</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>application/json</td>
            <td>1</td>
            <td>application/json;q=1</td>
        </tr>
        <tr>
            <td>application/xml</td>
            <td>0.7</td>
            <td>application/xml;q=0.7</td>
        </tr>
        <tr>
            <td>text/html</td>
            <td>0.3</td>
            <td>text/html;q=0.3</td>
        </tr>
    </tbody>
</table>

The resultant server preference string would look like ```application/json;q=1,application/xml;q=0.7,text/html;q=0.3```.



## ConNeg Pipeline

Content negotiation is performed in three stages:

* *Parsing*: The client & server preferences are parsed into arrays of [```Preference```](https://github.com/ptlis/conneg/blob/master/src/Preference/Preference.php) instances.
* *[Variant Matching](#variant-matching)*: The client & server preferences paired by applying the matching rules, resulting in an array of [```MatchedPreference```](https://github.com/ptlis/conneg/blob/master/src/Preference/Matched/MatchedPreference.php) instances.
* *Sorting*: Matched preferences are sorted by the product of the client & server quality factors, the one with the largest value is the preferred variant.



## Variant Matching

Each variant specified by the server is compared to the variants provided by the client using each of the matching rules described below. The successful match with the highest precedence is applied and a [```MatchedPreference```](https://github.com/ptlis/conneg/blob/master/src/Preference/Matched/MatchedPreference.php) is created from the client & server preferences. 

Once matching is complete the generated list of ```MatchedPreference``` is sorted by the product of the client & servers quality factors and the match with the highest value is the preferred type.

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
