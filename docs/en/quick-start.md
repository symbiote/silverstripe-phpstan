# Quick Start

1. Create "phpstan.neon" in project directory. For more configuration options, see [Advanced Usage](docs/en/advanced-usage.md).
```
includes:
    - phpstan/extension.neon
```

2. Execute from project dir:
`php ./vendor/phpstan/phpstan-shim/phpstan.phar analyse mysite/code -c "phpstan.neon" -a "vendor/silbinarywolf/silverstripe-phpstan/bootstrap.php" --level 2`

3. Visit the [PHPStan Github](https://github.com/phpstan/phpstan) for additional information. Try adjusting the `--level`, lower = less strict, higher = more strict.
