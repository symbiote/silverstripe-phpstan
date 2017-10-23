<?php

/**
 * This file is required to setup Silverstripe class autoloader
 */
$CORE_PATH = dirname(__FILE__).'/../framework/core';
//require_once($CORE_PATH.'/TempPath.php');
if (!file_exists($CORE_PATH.'/Core.php')) {
	echo 'Unable to find "framework" folder for Silverstripe 3.X project.';
	exit;
}
require_once($CORE_PATH.'/Core.php');

/**
 * This file is required to include additional Rule classes, etc.
 */
$SRC_FOLDER = dirname(__FILE__).'/src';

require_once($SRC_FOLDER.'/type/DataListType.php');

require_once($SRC_FOLDER.'/reflection/CachedMethod.php');
require_once($SRC_FOLDER.'/reflection/ViewableDataGetProperty.php');
require_once($SRC_FOLDER.'/reflection/ComponentHasOneProperty.php');
require_once($SRC_FOLDER.'/reflection/ComponentDBFieldProperty.php');
require_once($SRC_FOLDER.'/reflection/ComponentHasOneMethod.php');
require_once($SRC_FOLDER.'/reflection/ComponentHasManyMethod.php');
require_once($SRC_FOLDER.'/reflection/ComponentManyManyMethod.php');

require_once($SRC_FOLDER.'/DBFieldStaticReturnTypeExtension.php');
require_once($SRC_FOLDER.'/DataObjectGetStaticReturnTypeExtension.php');
require_once($SRC_FOLDER.'/DataObjectReturnTypeExtension.php');
require_once($SRC_FOLDER.'/DataListReturnTypeExtension.php');
require_once($SRC_FOLDER.'/PropertyClassReflectionExtension.php');
require_once($SRC_FOLDER.'/MethodClassReflectionExtension.php');
