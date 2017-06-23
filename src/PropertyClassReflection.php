<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use \ReflectionClass;
use \ReflectionMethod;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Type\ObjectType;

// Silverstripe
use Object;
use Config;
use DataObject;
use ContentController;

class PropertyClassReflection implements \PHPStan\Reflection\PropertiesClassReflectionExtension {
    /** @var \PHPStan\Reflection\PropertyReflection[][] */
    private $properties = [];

    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        if (!isset($this->properties[$classReflection->getName()])) {
            $this->properties[$classReflection->getName()] = $this->createProperties($classReflection);
        }

        return isset($this->properties[$classReflection->getName()][$propertyName]);
    }

    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        return $this->properties[$classReflection->getName()][$propertyName];
    }

    /**
     * @param \PHPStan\Reflection\ClassReflection $classReflection
     * @return \PHPStan\Reflection\PropertyReflection[]
     */
    private function createProperties(ClassReflection $classReflection): array
    {
        if (!$classReflection->isSubclassOf(Object::class)) {
            return [];
        }

        $properties = [];

        $class = $classReflection->getName();
        $isDataObjectOrContentController = $classReflection->isSubclassOf(DataObject::class);

        // Handle Page_Controller where it has $failover
        // NOTE(Jake): This is not foolproof, but if people follow the general SS convention
        //             it'll work.
        if (strpos($class, '_Controller') !== FALSE &&
            $classReflection->isSubclassOf(ContentController::class)) {
            $class = str_replace('_Controller', '', $class);
            $isDataObjectOrContentController = true;
        }

        if ($isDataObjectOrContentController) {
            $properties['ID'] = new ComponentDBFieldProperty('ID', $classReflection, 'Int');
            $properties['Created'] = new ComponentDBFieldProperty('LastEdited', $classReflection, 'SS_Datetime');
            $properties['LastEdited'] = new ComponentDBFieldProperty('LastEdited', $classReflection, 'SS_Datetime');

            $dbFields = Config::inst()->get($class, 'db');
            if ($dbFields) {
                foreach ($dbFields as $propertyName => $type) {
                    // Ignore parameters
                    $type = explode('(', $type, 2);
                    $type = $type[0];

                    if (is_numeric($propertyName)) {
                        // Skip
                        continue;
                    }
                    $properties[$propertyName] = new ComponentDBFieldProperty($propertyName, $classReflection, $type);
                }
            }

            $hasOne = Config::inst()->get($class, 'has_one');
            if ($hasOne) {
                foreach ($hasOne as $propertyName => $type) {
                    // Ignore parameters
                    $type = explode('(', $type, 2);
                    $type = $type[0];

                    $propertyName = $propertyName.'ID';
                    $properties[$propertyName] = new ComponentHasOneProperty($propertyName, $classReflection);
                }
            }
        }

        return $properties;
    }
}