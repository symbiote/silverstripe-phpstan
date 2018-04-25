<?php declare(strict_types = 1);

// NOTE(Jake): 2018-04-05
//
// Workaround Composer Autoloader / PHPUnit not working as I expect.
// Not sure why it's not using the Composer autoloader.
//
include_once(dirname(__FILE__).'/ResolverTest.php');

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
