Negotiation
===========

[![GitHub Actions](https://github.com/diablomedia/Negotiation/actions/workflows/ci.yaml/badge.svg)](https://github.com/diablomedia/Negotiation/actions/workflows/ci.yaml)
[![Total
Downloads](https://poser.pugx.org/diablomedia/negotiation/downloads.png)](https://packagist.org/packages/diablomedia/negotiation)
[![Latest Stable
Version](https://poser.pugx.org/diablomedia/negotiation/v/stable.png)](https://packagist.org/packages/diablomedia/negotiation)

**Negotiation** is a standalone library without any dependencies that allows you
to implement [content
negotiation](https://tools.ietf.org/html/rfc7231#section-5.3) in your
application, whatever framework you use.  This library is based on [RFC
7231](https://tools.ietf.org/html/rfc7231). Negotiation is easy to use, and
extensively unit tested!

This is the Diablo Media fork of [William Durand's Negotiation](https://github.com/willdurand/Negotiation), maintained as `diablomedia/negotiation`. It supports PHP 8.2–8.5 and keeps the `Negotiation\` namespace.

> The following documentation links describe historical upstream releases:
>
> Documentation for version **1.x** is available here: [Negotiation 1.x
> documentation](https://github.com/willdurand/Negotiation/blob/1.x/README.md#usage).
>
> Documentation for version **2.x** is available here: [Negotiation 2.x
> documentation](https://github.com/willdurand/Negotiation/blob/2.x/README.md#usage).


Installation
------------

The recommended way to install Negotiation is through
[Composer](http://getcomposer.org/):

```bash
$ composer require diablomedia/negotiation
```


Usage Examples
--------------

### Media Type Negotiation

``` php
$negotiator = new \Negotiation\Negotiator();

$acceptHeader = 'text/html, application/xhtml+xml, application/xml;q=0.9, */*;q=0.8';
$priorities   = array('text/html; charset=UTF-8', 'application/json', 'application/xml;q=0.5');

$mediaType = $negotiator->getBest($acceptHeader, $priorities);

$value = $mediaType->getValue();
// $value == 'text/html; charset=UTF-8'
```

The `Negotiator` returns an instance of `Accept`, or `null` if negotiating the
best media type has failed.

### Language Negotiation

``` php
<?php

$negotiator = new \Negotiation\LanguageNegotiator();

$acceptLanguageHeader = 'en; q=0.1, fr; q=0.4, fu; q=0.9, de; q=0.2';
$priorities          = array('de', 'fu', 'en');

$bestLanguage = $negotiator->getBest($acceptLanguageHeader, $priorities);

$type = $bestLanguage->getType();
// $type == 'fu';

$quality = $bestLanguage->getQuality();
// $quality == 0.9
```

The `LanguageNegotiator` returns an instance of `AcceptLanguage`.

### Encoding Negotiation

``` php
<?php

$negotiator = new \Negotiation\EncodingNegotiator();
$encoding   = $negotiator->getBest($acceptHeader, $priorities);
```

The `EncodingNegotiator` returns an instance of `AcceptEncoding`.

### Charset Negotiation

``` php
<?php

$negotiator = new \Negotiation\CharsetNegotiator();

$acceptCharsetHeader = 'ISO-8859-1, UTF-8; q=0.9';
$priorities          = array('iso-8859-1;q=0.3', 'utf-8;q=0.9', 'utf-16;q=1.0');

$bestCharset = $negotiator->getBest($acceptCharsetHeader, $priorities);

$type = $bestCharset->getType();
// $type == 'utf-8';

$quality = $bestCharset->getQuality();
// $quality == 0.81
```

The `CharsetNegotiator` returns an instance of `AcceptCharset`.

### `Accept*` Classes

`Accept` and `Accept*` classes share common methods such as:

* `getValue()` returns the accept value (e.g. `text/html; z=y; a=b; c=d`)
* `getNormalizedValue()` returns the value with parameters sorted (e.g.
  `text/html; a=b; c=d; z=y`)
* `getQuality()` returns the quality if available (`q` parameter)
* `getType()` returns the accept type (e.g. `text/html`)
* `getParameters()` returns the set of parameters (excluding the `q` parameter
  if provided)
* `getParameter()` allows to retrieve a given parameter by its name. Fallback to
  a `$default` (nullable) value otherwise.
* `hasParameter()` indicates whether a parameter exists.


Versioning
----------

Negotiation follows [Semantic Versioning](http://semver.org/).

The upstream 1.x and 2.x releases are no longer supported. This fork is based
on the upstream 3.x library. Its Composer metadata replaces the upstream package
at the fork's own version (`self.version`).

Development
-----------

Install the development dependencies:

```bash
composer install
```

Run all quality checks:

```bash
composer check
```

Individual commands are also available:

```bash
composer test      # PHPUnit
composer analyse  # PHPStan (level 8)
composer cs:check  # Check PER Coding Style 3.0
composer cs:fix    # Apply the coding standard
```

CI runs these checks on PHP 8.2, 8.3, 8.4, and 8.5 with both the highest and
lowest compatible dependencies. To reproduce a lowest-dependency run locally:

```bash
composer update --prefer-lowest --prefer-stable
composer check
```

Use `composer update --prefer-stable` to restore the latest compatible dependencies.
Composer selects PHPUnit 11 on PHP 8.2, PHPUnit 12 on PHP 8.3, and PHPUnit 13
on PHP 8.4 and 8.5 for highest-dependency runs.


The CI security job runs zizmor to audit GitHub Actions and fails on findings.
To run it locally, use `uvx zizmor .github` (or `zizmor .github` if installed).
Dependabot checks Composer packages and GitHub Actions weekly and groups minor
and patch version updates for each ecosystem; major updates get separate PRs.
Version updates have a seven-day cooldown to satisfy the zizmor audit; security
updates are not delayed by this cooldown.

CI generates a Clover coverage report and uploads the PHP 8.5 highest-dependency
report to Codecov. Enable this repository in Codecov and add its upload token as
the repository or organization Actions secret `CODECOV_TOKEN`. Public fork pull
requests can upload without that secret using Codecov's tokenless fork support.


Contributing
------------

See [CONTRIBUTING](CONTRIBUTING.md) file.


Credits
-------

* Some parts of this library are inspired by:

    * [Symfony](http://github.com/symfony/symfony) framework;
    * [FOSRest](http://github.com/FriendsOfSymfony/FOSRest);
    * [PEAR HTTP2](https://github.com/pear/HTTP2).

* [William Durand](https://github.com/willdurand)
* [@neural-wetware](https://github.com/neural-wetware)


License
-------

Negotiation is released under the MIT License. See the bundled LICENSE file for
details.
