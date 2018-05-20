<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PHPStan\Type\ObjectType;

// SilverStripe
use Config;

class ConfigHelper
{
    /**
     * @param string $className
     * @param string $configKey
     * @return array|scalar
     */
    public static function get($className, $configKey)
    {
        return Config::inst()->get($className, $configKey);
    }

    /**
     * @param string $className
     * @param string $configKey
     * @param string $configValue
     * @return array|scalar
     */
    public static function update($className, $configKey, $configValue)
    {
        return Config::inst()->update($className, $configKey, $configValue);
    }

    /**
     * @param string $className
     * @return ObjectType[]
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
        if ($extensions && isset($extensions[ClassHelper::Versioned])) {
            $dbFields['RecordID'] = 'Int';
        }

        $db = self::get($className, 'db');
        if ($db) {
            foreach ($db as $propertyName => $type) {
                // Ignore parameters
                $type = explode('(', $type, 2);
                $type = $type[0];
                if (isset($dbFields[$propertyName])
                    || is_numeric($propertyName)
                ) {
                    // Skip erroneous double-ups and skip numeric names
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
     * @return bool[]
     */
    public static function get_has_one($className)
    {
        $hasOne = self::get($className, 'has_one');
        $properties = array();
        if ($hasOne) {
            foreach ($hasOne as $propertyName => $type) {
                // Ignore parameters
                $type = explode('(', $type, 2);
                $type = $type[0];

                $propertyName = $propertyName.'ID';
                if (isset($properties[$propertyName])) {
                    // Skip erroneous duplicates
                    continue;
                }
                $properties[$propertyName] = true;
            }
        }
        return $properties;
    }

    /**
     * @return string[]
     */
    public static function get_extensions($className)
    {
        $extensionClasses = array();
        $extensions = self::get($className, 'extensions');
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
