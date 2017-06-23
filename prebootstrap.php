<?php 

/**
 * This file is required to include additional Rule classes, etc.
 */

require_once(dirname(__FILE__).'/src/ComponentHasOneProperty.php');
require_once(dirname(__FILE__).'/src/ComponentDBFieldProperty.php');
require_once(dirname(__FILE__).'/src/PropertyClassReflection.php');

require_once(dirname(__FILE__).'/src/AnyMethod.php');
require_once(dirname(__FILE__).'/src/ComponentHasOneMethod.php');
require_once(dirname(__FILE__).'/src/ComponentManyMethod.php');
require_once(dirname(__FILE__).'/src/MethodClassReflection.php');

// Module-specific
require_once(dirname(__FILE__).'/src/AbstractQueuedJobProperty.php');
require_once(dirname(__FILE__).'/src/AbstractQueuedJobPropertyClassReflection.php');