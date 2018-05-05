<?php declare(strict_types = 1);

// Use Composer autoloader
$path = dirname(__FILE__).'/../../../autoload.php';
require_once($path);

// Use SilverStripe manifest
$PROJECT_DIR = dirname(__FILE__).'/../../../..';
//
// This file is required to setup Silverstripe class autoloader
//
$CORE_PATH = $PROJECT_DIR.'/framework/core';
if (!file_exists($CORE_PATH.'/Core.php')) {
    echo 'Unable to find "framework" folder for Silverstripe 3.X project.';
    exit;
}
require_once $CORE_PATH.'/Core.php';


/*require_once dirname(__FILE__).'/Constants.php';

// Fall back to Composer's autoloader (e.g. for PHPUnit), if composer is used
if(file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}
// Int/Float autoloader for PHP5.6 backwards-compatability
require_once(BASE_PATH . '/framework/model/fieldtypes/compat/autoload.php');*/
