# PHPStan for Silverstripe

[![Build Status](https://travis-ci.org/symbiote/silverstripe-phpstan.svg?branch=master)](https://travis-ci.org/symbiote/silverstripe-phpstan)
[![Latest Stable Version](https://poser.pugx.org/symbiote/silverstripe-phpstan/version.svg)](https://github.com/symbiote/silverstripe-phpstan/releases)
[![Latest Unstable Version](https://poser.pugx.org/symbiote/silverstripe-phpstan/v/unstable.svg)](https://packagist.org/packages/symbiote/silverstripe-phpstan)
[![Total Downloads](https://poser.pugx.org/symbiote/silverstripe-phpstan/downloads.svg)](https://packagist.org/packages/symbiote/silverstripe-phpstan)
[![License](https://poser.pugx.org/symbiote/silverstripe-phpstan/license.svg)](https://github.com/symbiote/silverstripe-phpstan/blob/master/LICENSE.md)

An extension for PHPStan to allow it to reason about SilverStripe functionality.

**Features:**

- Support for `DataObject::get()`, ie. it understands you have a DataList of iterable SiteTree records.
- Support for DataObject `db`, `has_one`, `has_many` and `many_many` magic properties and methods, ie. it knows SiteTree::Title is a string, that SiteTree::ParentID is an integer and that SiteTree::Parent() is a SiteTree record.
- Support for `singleton('SiteTree')` and `Injector::inst()->get('SiteTree')`, ie. it knows these will return "SiteTree". If you override these with the injector, it'll also know what class you're actually using.

This PHPStan module is able to reason about extensions installed specific to your project as it bootstraps the SilverStripe config system. So if you've added an extension to your `Page` object that adds an additional `db` field, PHPStan will be able to reason about it.

## Composer Install

SilverStripe 3.X
```
composer require --dev symbiote/silverstripe-phpstan:1.0.0 phpstan/phpstan-shim:0.9.2
```

SilverStripe 4.X
```
composer require --dev symbiote/silverstripe-phpstan
```

NOTE: Versions of PHPStan less than 0.12, we recommend installing the phpstan-shim as currently in SilverStripe 3.X,
the QueuedJobs module's dependence on superclosure forces the PHP-Parser dependency of PHPStan to be at a very outdated
version. From 0.12, the 'shim' install is the dfeault.

## Requirements

* SilverStripe 3.1+

## Documentation

* [Quick Start](docs/en/quick-start.md)
* [Advanced Usage](docs/en/advanced-usage.md)
* [License](LICENSE.md)

## Known Limitations

* The type of the `owner` property can't be reasoned about for extensions. You must use `getOwner()`. Related Issues: [#1043](https://github.com/phpstan/phpstan/issues/1043) and [#1044](https://github.com/phpstan/phpstan/issues/1044)

## Credits

* [Ondřej Mirtes](https://github.com/ondrejmirtes) for his amazing work on the PHPStan library
* [Grant Lucas](https://github.com/symbiote/silverstripe-phpstan/pull/8) for his contribution to this library by upgrading it to PHPStan 0.9
