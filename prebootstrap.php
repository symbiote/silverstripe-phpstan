<?php 

/**
 * This file is required to include additional Rule classes, etc.
 */

$SRC_FOLDER = dirname(__FILE__).'/src';

require_once($SRC_FOLDER.'/ViewableDataGetProperty.php');
require_once($SRC_FOLDER.'/ComponentHasOneProperty.php');
require_once($SRC_FOLDER.'/ComponentDBFieldProperty.php');
require_once($SRC_FOLDER.'/PropertyClassReflection.php');

require_once($SRC_FOLDER.'/AnyMethod.php');
require_once($SRC_FOLDER.'/ComponentHasOneMethod.php');
require_once($SRC_FOLDER.'/ComponentManyMethod.php');
require_once($SRC_FOLDER.'/MethodClassReflection.php');

// Module-specific
require_once($SRC_FOLDER.'/AbstractQueuedJobProperty.php');
require_once($SRC_FOLDER.'/AbstractQueuedJobPropertyClassReflection.php');