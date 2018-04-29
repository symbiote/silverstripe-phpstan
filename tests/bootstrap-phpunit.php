<?php declare(strict_types = 1);

// NOTE(Jake): 2018-04-05
//
// Workaround Composer Autoloader / PHPUnit not working as I expect.
// Not sure why it's not using the Composer autoloader.
//
require_once(__DIR__.'/ResolverTest.php');

// Composer - vendor/autoload.php
require_once(__DIR__.'/../../../autoload.php');

// Load SilverStripe - For accessing YML/config
require_once(__DIR__.'/../bootstrap.php');

// Used by: RequestFilterPreRequestRuleTest
require_once(__DIR__.'/RequestFilterBad.php');
require_once(__DIR__.'/RequestFilterGood.php');
