PHPStan for Silverstripe
=========================

Warning: This is a work in progress, the instructions below are mostly for myself for the time being.

Quick Start
- Download PHPStan.phar from: https://github.com/phpstan/phpstan/releases
- Place in root directory of project
- While in path of project execute:
```
php7 phpstan.phar analyse {MODULE_FOLDER_HERE} -a "phpstan/service_autoloader.php" -c "phpstan/phpstan.neon"
```

Example use on Windows:
```
"/D/wamp/bin/php/php7.1.5/php" "/D/wamp/bin/php/php7.1.5/phpstan.phar" analyse mysite -c "phpstan/phpstan.neon" -a "phpstan/prebootstrap.php"
```