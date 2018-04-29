# PHPStan for Silverstripe

[![Build Status](https://travis-ci.org/silbinarywolf/silverstripe-phpstan.svg?branch=master)](https://travis-ci.org/silbinarywolf/silverstripe-phpstan)
[![Latest Stable Version](https://poser.pugx.org/silbinarywolf/silverstripe-phpstan/version.svg)](https://github.com/silbinarywolf/silverstripe-phpstan/releases)
[![Latest Unstable Version](https://poser.pugx.org/silbinarywolf/silverstripe-phpstan/v/unstable.svg)](https://packagist.org/packages/silbinarywolf/silverstripe-phpstan)
[![Total Downloads](https://poser.pugx.org/silbinarywolf/silverstripe-phpstan/downloads.svg)](https://packagist.org/packages/silbinarywolf/silverstripe-phpstan)
[![License](https://poser.pugx.org/silbinarywolf/silverstripe-phpstan/license.svg)](https://github.com/silbinarywolf/silverstripe-phpstan/blob/master/LICENSE.md)

Add PHPStan functionality to Silverstripe.

## Composer Install

```
composer require --dev silbinarywolf/silverstripe-phpstan:0.5.0 phpstan/phpstan-shim:0.9.2
```

NOTE: We recommend installing the phpstan-shim as currently in SilverStripe 3.X, the QueuedJobs module's dependence on superclosure forces the PHP-Parser dependency of PHPStan to be at a very outdated version.

## Requirements

* SilverStripe 3.1+

## Documentation

* [Quick Start](docs/en/quick-start.md)
* [Advanced Usage](docs/en/advanced-usage.md)
* [License](LICENSE.md)

## Credits

* [Ondřej Mirtes](https://github.com/ondrejmirtes) for his amazing work on the PHPStan library
* [Grant Lucas](https://github.com/silbinarywolf/silverstripe-phpstan/pull/8) for his contribution to this library by upgrading it to PHPStan 0.9
