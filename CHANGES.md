# Changelog:



## 3.0.0 - 2014-10-03

 *  Re-write of library to be PSR compliant & to improve implementation by using modern techniques.
 *  Component-based library - usable independently or through the Negotiate utility class.
 *  Comprehensive test suite.
 *  Installable with composer/packagist.
     

## 2.0.4 - 2013-03-28

 *  Fix for github Issue #3 (https://github.com/ptlis/conneg/issues/3) caused by the regex not accepting non-integer 
    quality factors that lack a prefixed '0'.
 *  Backport of unit tests from the version 3 dev branch.
 *  Calculated quality factors are always returned as a string.
 *  Number of return values is now consistent on parsing failure / success.
 *  If an accept-extension fragment is found in one type all other returned types will also have that key with a value 
    of null.
 *  Fix to sorting when both the user-agent and application provide quality factors including accept-extensions.


## 2.0.3 - 2013-03-09

 *  Fix for github Issue #2 (https://github.com/ptlis/conneg/issues/2), caused by the regex not accepting mime-types 
    that contain numbers.
       
       
## 2.0.2 - 2012-01-01

 *  Fix for call to conNeg::sortTypes() where no non-wildcard type is provided by the User Agent and the application 
    isn't providing it's own list of types. Thanks to Ben Companjen for the report that brought this to my attention.


## 2.0.1 - 2011-03-31

 *  Fix for conNeg::sortTypes() where the incorrect sort parameters would be generated if the application does not 
    provide quality factors, thanks go to seb (http://sebashton.com/) for this fix.


## 2.0.0 - 2010-01-28

 *  Significant refactor of the internals and a change in the API (a wrapper class is packaged in 
    compat/content_negotiation.inc.php that provides the same API as found in 1.3, simply have it and conNeg.inc.php
    in the directory your application expects to find the library).
 *  Application type data can now be provided in the form of a string conforming to the syntax and semantics of the 
    relevant header field in the HTTP/1.1 specification, section 14 (rfc2616 http://www.ietf.org/rfc/rfc2616.txt).
 *  The library now handles the accept-extension fragment in the Accept header.
 *  The library now handles mediatypes that contain numeric characters in the subtype - thanks again go to richard
    (http://code.google.com/u/@VhBSQ1BRBxZDVgB7/) for this bugfix.
 *  By default the generated data-structure is now sorted by the product of the application and user agent q factors 
    when the application provides them.


## 1.3.0 - 2008-11-01

 *  The main generic_negotiation function has been significantly refactored to simplify the algorithms implementation 
    and generally handle things more gracefully.
 *  Negotiation performed on headers without providing a list of types to look for no longer returns wildcard types.
 *  Negotiation performed on the charset, language and encoding headers (through the charset_*, language_* & encoding_* 
    functions) now supports wildcards.
 *  Handling of how the user agent and application quality factors are used to determine the preferred type has been 
    revised. The library now has a second mode where it sums the user agent and application quality factors and uses 
    this value to determine the preferred resource. This behavior can be enabled by appending true as a second parameter
    to any of the public functions. Thanks to richard (http://code.google.com/u/@VhBSQ1BRBxZDVgB7/) for this suggestion.


## 1.2.0 - 2007-12-25
 *  Support for php 4.x dropped being as the php developers will no longer be supporting php 4 as of the 31st December.
 *  Support for wildcard rules implemented.
 *  No longer requires a list of types to look for, if there is no parameter passed to the negotiation functions then 
    they generate a list internally from the browser's headers.
 *  Fixed the XHTML & HTML negotiation class so that it works as intended.


## 1.1.0 - 2006-12-05

 *  Significant re-write to encapsulate functionality within a class.
 *  There are now two versions, a version targeted at the PHP 4.x releases and a version targeted at the 5.x releases
    that takes advantage of the improved support for OOP techniques.
 *  There is now a separate include file that can be used to determine if a browser can handle XHTML, and if it can 
    whether it has a preference towards it or HTML.


## 1.0.2 - 2006-02-07

 *  Replaced the inner for loop and conditional with the use of the array_search function - my thanks go to NeoThermic
    for telling me about this function.


## 1.0.1 - 2006-01-23

 *  Added strtolower into parsing so that comparisons of media-types can be done with the '===' php identical operator 
    without worrying about case.


## 1.0.0 - 2006-01-19 

 *  Initial public release.
