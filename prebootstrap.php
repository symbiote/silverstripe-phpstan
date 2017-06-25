<?php 

/**
 * This file is required to include additional Rule classes, etc.
 */

$SRC_FOLDER = dirname(__FILE__).'/src';

require_once($SRC_FOLDER.'/type/DataListType.php');

require_once($SRC_FOLDER.'/reflection/AnyMethod.php');
require_once($SRC_FOLDER.'/reflection/ViewableDataGetProperty.php');
require_once($SRC_FOLDER.'/reflection/ComponentHasOneProperty.php');
require_once($SRC_FOLDER.'/reflection/ComponentDBFieldProperty.php');
require_once($SRC_FOLDER.'/reflection/ComponentHasOneMethod.php');
require_once($SRC_FOLDER.'/reflection/ComponentHasManyMethod.php');
require_once($SRC_FOLDER.'/reflection/ComponentManyManyMethod.php');

// Services
require_once($SRC_FOLDER.'/PropertyClassReflectionExtension.php');
require_once($SRC_FOLDER.'/MethodClassReflectionExtension.php');
