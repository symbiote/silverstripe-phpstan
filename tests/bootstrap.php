<?php declare(strict_types = 1);

// vendor/autoload.php
require_once __DIR__.'/../../../autoload.php';

// NOTE(Jake): 2018-04-21
//
// Taken from phpstan/phpstan-phpunit:
// - https://raw.githubusercontent.com/phpstan/phpstan-phpunit/master/tests/bootstrap.php
//

//use PHPStan\Type\TypeCombinator;

//TypeCombinator::setUnionTypesEnabled(true);

// Load SilverStripe
require_once __DIR__.'/../bootstrap.php';
