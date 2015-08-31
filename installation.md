---
layout: code
name: installation
---

# Installation

Either from the console:

~~~bash
composer require ptlis/conneg-bundle:"~3.0.0"
~~~

Or by Editing composer.json:

~~~json
{
    "require": {
        "ptlis/conneg-bundle": "~3.0.0"
    }
}
~~~

Followed by a composer update:

~~~bash
composer update
~~~


After installation you need to include Composer's autoloader:

~~~php
require 'vendor/autoload.php';
~~~
