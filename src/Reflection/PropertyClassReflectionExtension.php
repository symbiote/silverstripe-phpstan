<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Reflection;

use ReflectionClass;
use ReflectionMethod;
use Exception;
use Symbiote\SilverstripePHPStan\ClassHelper;
use Symbiote\SilverstripePHPStan\ConfigHelper;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Type\ObjectType;

class PropertyClassReflectionExtension implements \PHPStan\Reflection\PropertiesClassReflectionExtension
{
    /**
     *
     *
     * @var \PHPStan\Reflection\PropertyReflection[][]
     */
    private $properties = [];

    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        $class = $classReflection->getName();
        if (!isset($this->properties[$class])) {
            $this->properties[$class] = $this->createProperties($classReflection);
        }
        $result = isset($this->properties[$class][$propertyName]);
        return $result;
    }

    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        $class = $classReflection->getName();
        if (!isset($this->properties[$class])) {
            $this->properties[$class] = $this->createProperties($classReflection);
        }
        if (isset($this->properties[$class][$propertyName])) {
            return $this->properties[$class][$propertyName];
        }
        if ($classReflection->isSubclassOf(ClassHelper::ViewableData)) {
            // ViewableData has a magic __get() method that always at least
            // returns 'null'.
            //
            // If we couldn't determine a property from `get` methods or extensions.
            //
            $this->properties[$class][$propertyName] = new ViewableDataGetNullProperty($propertyName, $classReflection);
            return $this->properties[$class][$propertyName];
        }
        throw new Exception('This should not happen.');
    }

    /**
     * @param \PHPStan\Reflection\ClassReflection $classReflection
     * @return \PHPStan\Reflection\PropertyReflection[]
     */
    private function createProperties(ClassReflection $classReflection): array
    {
        if (!$classReflection->isSubclassOf(ClassHelper::ViewableData)) {
            return [];
        }

        $properties = [];

        $class = $classReflection->getName();
        $isDataObjectOrContentController = $classReflection->getName() === ClassHelper::DataObject ||
                                            $classReflection->isSubclassOf(ClassHelper::DataObject);

        // Get extension classes
        $extensionClasses = array();
        $extensions = ConfigHelper::get($class, 'extensions');
        if ($extensions) {
            foreach ($extensions as $extensionClass) {
                // Ignore parameters (ie. "Versioned('Stage', 'Live')")
                $extensionClass = explode('(', $extensionClass, 2);
                $extensionClass = $extensionClass[0];

                $extensionClasses[$extensionClass] = $extensionClass;
            }
        }
        unset($extensions);

        // Handle magic properties that use 'get$Method' on main class
        if ($classReflection->isSubclassOf(ClassHelper::ViewableData)) {
            $classesToGetFrom = [$class];
            if ($extensionClasses) {
                $classesToGetFrom = array_merge($classesToGetFrom, $extensionClasses);
            }
            foreach ($classesToGetFrom as $getMethodPropClass) {
                // Ignore parameters (ie. "Versioned('Stage', 'Live')")
                $getMethodPropClass = explode('(', $getMethodPropClass, 2);
                $getMethodPropClass = $getMethodPropClass[0];

                foreach (get_class_methods($getMethodPropClass) as $method) {
                    if (substr($method, 0, 3) !== 'get') {
                        continue;
                    }
                    $property = substr($method, 3);
                    // todo(Jake): Better way to handle properties, if someone does '$this->myPrOp'
                    //             it should work with 'getMyProp' since PHP method aren't case sensitive.
                    $propInstance = new ViewableDataGetProperty($property, $classReflection);
                    if (!isset($properties[$property])) {
                        $properties[$property] = $propInstance;
                    }
                    // ie. getOwner() -> owner
                    $propertyToLower = strtolower($property);
                    if (!isset($properties[$propertyToLower])) {
                        $properties[$propertyToLower] = $propInstance;
                    }
                }
            }
        }

        // Handle Page_Controller where it has $failover
        // NOTE(Jake): This is not foolproof, but if people follow the general SS convention
        //             it'll work.
        if (strpos($class, '_Controller') !== false
            && $classReflection->isSubclassOf(ClassHelper::ContentController)
        ) {
            $class = str_replace('_Controller', '', $class);
            $isDataObjectOrContentController = true;
        }

        if ($isDataObjectOrContentController) {
            $dbFields = ConfigHelper::get_db($class);
            if ($dbFields) {
                foreach ($dbFields as $propertyName => $type) {
                    $properties[$propertyName] = new ComponentDBFieldProperty($propertyName, $classReflection, $type);
                }
            }

            $hasOne = ConfigHelper::get_has_one($class);
            if ($hasOne) {
                foreach ($hasOne as $propertyName => $_) {
                    $properties[$propertyName] = new ComponentHasOneProperty($propertyName, $classReflection);
                }
            }
        }

        return $properties;
    }
}
