<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;
use PHPStan\Type\ArrayType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ThisType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\UnionType;

// SilverStripe
use Extension;
use Object;
use Config;

class ExtensionReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{
    protected $ownerClassNamesByExtensionClassName = null;

    public function getClass(): string
    {
        return Extension::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        $name = $methodReflection->getName();
        switch ($name) {
            case 'getOwner':
                return true;
            break;
        }
        return false;
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        $name = $methodReflection->getName();

        switch ($name) {
            // NOTE(Jake): 2018-04-25
            //
            // Ideally this would work with both '$this->owner' and '$this->getOwner()'
            //
            // However there doesn't seem to be a `DynamicPropertyReturnTypeExtension` hook and I'm
            // not sure on how I can apply that type info.
            //
            case 'getOwner':
                // Get the type of the `Extension` subclass
                $type = $scope->getType($methodCall->var);
                $objectType = null;
                if ($type instanceof ThisType) {
                    $objectType = new ObjectType($type->getClassName());
                } else {
                    $objectType = Utility::getTypeFromVariable($methodCall->var, $methodReflection);
                }
                if (!$objectType) {
                    return $methodReflection->getReturnType();
                }

                // Lookup if this extension is configured by any class to be used in their 'extensions'
                $extensionClassName = $objectType->getClassName();
                $ownerClassNamesByExtensionClassName = $this->getOwnerClassNamesByExtensionClassName();
                if (!isset($ownerClassNamesByExtensionClassName[$extensionClassName])) {
                    return $methodReflection->getReturnType();
                }
                $classesUsingExtension = $ownerClassNamesByExtensionClassName[$extensionClassName];

                //
                $types = [];
                if ($classesUsingExtension) {
                    foreach ($classesUsingExtension as $class) {
                        // Ignore classes that don't exist.
                        if (!class_exists($class)) {
                            continue;
                        }
                        $types[] = new ObjectType($class);
                    }
                }
                if (!$types) {
                    return $methodReflection->getReturnType();
                }
                if (count($types) === 1) {
                    // NOTE(Jake): 2018-04-25
                    //
                    // UnionType does not allow multiple types to be passed in
                    //
                    //
                    return $types[0];
                }
                return new UnionType($types);
            break;

            default:
                throw Exception('Unhandled method call: '.$name);
            break;
        }
        return $methodReflection->getReturnType();
    }

    private function getOwnerClassNamesByExtensionClassName()
    {
        if ($this->ownerClassNamesByExtensionClassName !== null) {
            return $this->ownerClassNamesByExtensionClassName;
        }
        $extensionToClassName = array();
        $classes = $this->getSubclassesOf(Object::class);
        foreach ($classes as $class) {
            $extensions = Config::inst()->get($class, 'extensions');
            if (!$extensions) {
                continue;
            }
            foreach ($extensions as $extension) {
                if (!$extension) {
                    continue;
                }
                $extensionToClassName[$extension][$class] = $class;
            }
        }
        return $this->ownerClassNamesByExtensionClassName = $extensionToClassName;
    }

    private function getSubclassesOf($parentClass)
    {
        $result = array();
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, $parentClass)) {
                $result[] = $class;
            }
        }
        return $result;
    }
}
