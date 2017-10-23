# PHPStan for Silverstripe

1. Create "phpstan.neon" in project dir.
```
includes:
	- phpstan/extension.neon
```

2. Execute from project dir:
`php ./vendor/phpstan/phpstan-shim/phpstan.phar analyse mysite --level 0 -c "phpstan.neon" -a "phpstan/bootstrap.php"`

3. Visit the [PHPStan Github](https://github.com/phpstan/phpstan) for additional information.

## Composer Install

```
composer require silbinarywolf/silverstripe-phpstan:~1.0.0
```

## Requirements

* SilverStripe 3.1+

## Documentation

* [License](LICENSE.md)
