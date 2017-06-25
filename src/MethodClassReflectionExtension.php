<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use \ReflectionClass;
use \ReflectionMethod;

use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\BrokerAwareClassReflectionExtension;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Type\ObjectType;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\Php\PhpMethodReflection;

// Silverstripe
use Object;
use Config;
use DataObject;
use ContentController;

class MethodClassReflectionExtension implements MethodsClassReflectionExtension, BrokerAwareClassReflectionExtension {
    /** @var MethodReflection[][] */
    private $methods = [];

    /** @var Broker */
    private $broker;

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool 
    {
        if (!isset($this->methods[$classReflection->getName()])) {
            $this->methods[$classReflection->getName()] = $this->createMethods($classReflection);
        }

        return isset($this->methods[$classReflection->getName()][$methodName]);
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        // Fallback to has_one/has_many/many_many
        return $this->methods[$classReflection->getName()][$methodName];
    }

    public function setBroker(Broker $broker) {
        $this->broker = $broker;
    }

    /**
     * @param ClassReflection $classReflection
     * @return MethodReflection[]
     */
    private function createMethods(ClassReflection $classReflection): array
    {
        if (!$classReflection->isSubclassOf(Object::class)) {
            return [];
        }

        $methods = [];

        $class = $classReflection->getName();
        $isDataObjectOrContentController = $classReflection->isSubclassOf(DataObject::class);

        // Add methods from extensions
        $extensionInstances = Config::inst()->get($class, 'extensions');
        if ($extensionInstances) {
            foreach ($extensionInstances as $extensionClass) {
                // Ignore parameters
                // ie. Extract "Versioned" from "Versioned('Stage', 'Live')"
                $extensionClass = explode('(', $extensionClass, 2);
                $extensionClass = $extensionClass[0];

                $extensionClassReflection = $this->broker->getClass($extensionClass);
                foreach (get_class_methods($extensionClass) as $methodName) {
                    /** @var $methodReflection PhpMethodReflection */
                    $methodReflection = $extensionClassReflection->getMethod($methodName);
                    $methods[$methodName] = $methodReflection;
                }
            }
        }


        // Detect little-known Silverstripe '_' cache function
        // ie. Define: function _MyFunction()
        //     Call: $this->MyFunction() will be cached.
        //
        foreach (get_class_methods($class) as $methodName) {
            if ($methodName && $methodName[0] === '_') {
                $methodName = substr($methodName, 1);
                $methods[$methodName] = new AnyMethod($methodName, $classReflection);
            }
        }

        // Handle Page_Controller where it has $failover
        // NOTE(Jake): This is not foolproof, but if people follow the general SS convention
        //             it'll work.
        if (strpos($class, '_Controller') !== FALSE &&
            $classReflection->isSubclassOf(ContentController::class)) {
            $class = str_replace('_Controller', '', $class);
            $isDataObjectOrContentController = true;

            $failoverClassReflection = $this->broker->getClass($class);
            foreach (get_class_methods($class) as $methodName) {
                /** @var $methodReflection PhpMethodReflection */
                $methodReflection = $failoverClassReflection->getMethod($methodName);
                $methods[$methodName] = $methodReflection;
            }
        }

        // todo(Jake): Figure out if an extension magic __call() has precedence over a has_one magic call
        if ($isDataObjectOrContentController) {
            $components = array(
                'has_one' => ComponentHasOneMethod::class,
                'belongs_to' => ComponentHasOneMethod::class,
                'has_many' => ComponentHasManyMethod::class,
                'many_many' => ComponentManyManyMethod::class,
                'belongs_many_many' => ComponentManyManyMethod::class,
            );
            foreach ($components as $componentType => $componentClass) {
                $componentNameValueMap = Config::inst()->get($class, $componentType);
                if (!$componentNameValueMap) {
                    continue;
                }

                foreach ($componentNameValueMap as $methodName => $type) {
                    // Ignore parameters
                    $type = explode('(', $type, 2);
                    $type = $type[0];

                    $methods[$methodName] = new $componentClass($methodName, $classReflection, new ObjectType($type));
                }
            }
        }

        return $methods;
    }
}