<?php 

/**
 * This file is required to setup the autoloader
 */
$CORE_PATH = dirname(__FILE__).'/../framework/core';
require_once($CORE_PATH.'/TempPath.php');

// Copy-pasted from framework/Constants.php
/*$candidateBasePath = rtrim(dirname(dirname(dirname(__FILE__))), DIRECTORY_SEPARATOR);
if($candidateBasePath == '') $candidateBasePath = DIRECTORY_SEPARATOR;
define('BASE_PATH', $candidateBasePath);

// Override silverstripe-cache username to 'phpstan'
define('TEMP_FOLDER', getTempParentFolder(BASE_PATH) . DIRECTORY_SEPARATOR . 'phpstan');*/

require_once($CORE_PATH.'/Core.php');