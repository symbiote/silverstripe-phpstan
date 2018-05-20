<?php

// Use Composer autoloader
$path = dirname(__FILE__).'/../../autoload.php';
require_once($path);

// Check if incorrect SilverStripe version (ie. 4+)
$coreFilepath = dirname(__FILE__).'/../../silverstripe/framework';
if (file_exists($coreFilepath)) {
    echo 'This version of PHPStan is for SilverStripe 3.X projects, not SilverStipe 4.X.';
    exit(2);
}

//
// This file is required to setup Silverstripe class autoloader
//
$projectDir = dirname(__FILE__).'/../../..';
$coreFilepath = $projectDir.'/framework/core/Core.php';
if (!file_exists($coreFilepath)) {
    echo 'Unable to find "framework" folder for Silverstripe 3.X project.';
    exit(1);
}
require_once($coreFilepath);
