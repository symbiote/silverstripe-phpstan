<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Reflection;

use ReflectionClass;
use ReflectionMethod;
use Exception;
use Symbiote\SilverstripePHPStan\ClassHelper;
use Symbiote\SilverstripePHPStan\ConfigHelper;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Type\ObjectType;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Analyser\Scope;

class MethodClassReflectionExtension implements MethodsClassReflectionExtension, BrokerAwareExtension
{
    /**
     *
     *
     * @var MethodReflection[][]
     */
    private $methods = [];

    /**
     *
     *
     * @var Broker
     */
    private $broker;

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (!isset($this->methods[$classReflection->getName()])) {
            $this->methods[$classReflection->getName()] = $this->createMethods($classReflection);
        }
        return isset($this->methods[$classReflection->getName()][strtolower($methodName)]);
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        if (!isset($this->methods[$classReflection->getName()])) {
            $this->methods[$classReflection->getName()] = $this->createMethods($classReflection);
        }
        // Fallback to has_one/has_many/many_many
        return $this->methods[$classReflection->getName()][strtolower($methodName)];
    }

    public function setBroker(Broker $broker): void
    {
        $this->broker = $broker;
    }

    /**
     * @param ClassReflection $classReflection
     * @return MethodReflection[]
     */
    private function createMethods(ClassReflection $classReflection): array
    {
        if (!$classReflection->isSubclassOf(ClassHelper::ViewableData)) {
            return [];
        }

        $methods = [];

        $class = $classReflection->getName();
        $isDataObjectOrContentController = $classReflection->getName() === ClassHelper::DataObject ||
                                            $classReflection->isSubclassOf(ClassHelper::DataObject);

        // Add methods from extensions
        $extensionInstances = ConfigHelper::get_extensions($class);
        if ($extensionInstances) {
            foreach ($extensionInstances as $extensionClass) {
                $extensionClassReflection = $this->broker->getClass($extensionClass);
                foreach (get_class_methods($extensionClass) as $methodName) {
                    $methodReflection = $extensionClassReflection->getNativeMethod($methodName);
                    $methods[strtolower($methodName)] = $methodReflection;
                }
            }
        }

        // Detect little-known Silverstripe '_' cache function
        // ie. Define: function _MyFunction()
        //     Call: $this->MyFunction() will be cached.
        //
        foreach (get_class_methods($class) as $methodName) {
            if ($methodName && $methodName[0] === '_' && isset($methodName[1]) && $methodName[1] !== '_') {
                $uncachedMethodName = substr($methodName, 1);
                $methods[strtolower($uncachedMethodName)] = new CachedMethod($classReflection->getNativeMethod($methodName));
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

            $failoverClassReflection = $this->broker->getClass($class);
            foreach (get_class_methods($class) as $methodName) {
                $methodReflection = $failoverClassReflection->getNativeMethod($methodName);
                $methods[strtolower($methodName)] = $methodReflection;
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
                $componentNameValueMap = ConfigHelper::get($class, $componentType);
                if (!$componentNameValueMap) {
                    continue;
                }

                foreach ($componentNameValueMap as $methodName => $type) {
                    if (is_array($type)) {
                        if ($componentType !== 'many_many') {
                            throw new Exception('Cannot use array format for "'.$componentType.'" component type.');
                        }
                        if (!isset($type['through']) ||
                            !isset($type['from']) ||
                            !isset($type['to'])) {
                            throw new Exception('Unknown array format. Expected string or array with "through", "from" and "to".');
                        }
                        // Example data:
                        // array(3) {["through" => "SilverStripe\Assets\Shortcodes\FileLink"]
                        //           ["from" => "Parent"]
                        //           ["to" => "Linked"]
                        $toClass = $type['to'];
                        $throughClass = $type['through'];
                        $throughClassHasOne = ConfigHelper::get($throughClass, 'has_one');
                        if ($throughClassHasOne && isset($throughClassHasOne[$toClass])) {
                            $type = $throughClassHasOne[$toClass];
                        }
                    }
                    // Ignore parameters
                    $type = explode('(', $type, 2);
                    $type = $type[0];
                    $componentMethodClass = new $componentClass($methodName, $classReflection, new ObjectType($type));
                    $methods[strtolower($methodName)] = $componentMethodClass;
                }
            }
        }
        return $methods;
    }
}
