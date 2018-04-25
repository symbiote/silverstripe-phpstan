<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

// SilverStripe
use Config;
use Injector;
use Versioned;

class ConfigHelper
{
    /**
     * @var array
     */
    public static function get_db($className)
    {
        $dbFields = array();
        // NOTE(Jake): 2018-04-25
        //
        // Support the default DataObject DB fields
        //
        $dbFields = array(
            'ID' => 'Int', // NOTE: DBInt in SS 3.6+ and 4.0
            'ClassName' => 'Enum',
            'Created' => 'SS_Datetime',
            'LastEdited' => 'SS_Datetime',
        );
        // Support Versioned fields for when grabbing records out of *_versions tables.
        $extensions = self::get_extensions($className);
        if ($extensions && isset($extensions[Versioned::class])) {
            $dbFields['RecordID'] = 'Int';
        }

        $db = Config::inst()->get($className, 'db');
        if ($db) {
            foreach ($db as $propertyName => $type) {
                // Ignore parameters
                $type = explode('(', $type, 2);
                $type = $type[0];
                if (isset($properties[$propertyName])
                    || is_numeric($propertyName)
                ) {
                    // Skip
                    continue;
                }
                $dbFields[$propertyName] = $type;
            }
        }
        foreach ($dbFields as $propertyName => $type) {
            $dbFields[$propertyName] = Utility::getClassFromInjectorString($type);
        }
        return $dbFields;
    }

    /**
     * @var array
     */
    public static function get_extensions($className)
    {
        $extensionClasses = array();
        $extensions = Config::inst()->get($className, 'extensions');
        if ($extensions) {
            foreach ($extensions as $extensionClass) {
                // Ignore parameters (ie. "Versioned('Stage', 'Live')")
                $extensionClass = explode('(', $extensionClass, 2);
                $extensionClass = $extensionClass[0];

                $extensionClasses[$extensionClass] = $extensionClass;
            }
        }
        return $extensionClasses;
    }
}
