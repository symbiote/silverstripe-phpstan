<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use Exception;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Broker\Broker;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Expr\ClassConstFetch;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;
use PHPStan\Type\ArrayType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;

// Silverstripe
use DataObject;
use Config;

class DataObjectReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return DataObject::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        $name = $methodReflection->getName();
        switch ($name) {
            case 'dbObject':
                return true;
            break;

            case 'newClassInstance':
                return true;
            break;
        }
        return false;
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        $type = null;
        $type = $scope->getType($methodCall->var);

        $name = $methodReflection->getName();
        switch ($name) {
            case 'dbObject':
                $className = '';
                if ($type instanceof StaticType) {
                    if (count($type->getReferencedClasses()) === 1) {
                        $className = $type->getReferencedClasses()[0];
                    }
                } else {
                    return $methodReflection->getReturnType();
                }
                if (!$className) {
                    return $methodReflection->getReturnType();
                }
                $dbFields = Config::inst()->get($className, 'db');
                if (count($methodCall->args) === 0) {
                    return $methodReflection->getReturnType();
                }
                // Handle $this->dbObject('Field')
                $arg = $methodCall->args[0]->value;
                $value = '';
                if ($arg instanceof Variable) {
                    // Unhandled, cannot retrieve variable value even if set in this scope.
                    return $methodReflection->getReturnType();
                } else if ($arg instanceof ClassConstFetch) {
                    // Handle "SiteTree::class" constant
                    $value = (string)$arg->class;
                } else if ($arg instanceof String_) {
                    $value = $arg->value;
                }
                if (!$value) {
                    throw new Exception('Mishandled "newClassInstance" call.');
                    //return $methodReflection->getReturnType();
                }
                if (!isset($dbFields[$value])) {
                    return $methodReflection->getReturnType();
                }
                $dbFieldType = $dbFields[$value];

                // Ignore parameters
                // ie. Extract "Enum" from "Enum('Value1', 'Value2')"
                $dbFieldType = explode('(', $dbFieldType, 2);
                $dbFieldType = $dbFieldType[0];

                return new ObjectType($dbFieldType);
            break;

            case 'newClassInstance':
                if (count($methodCall->args) === 0) {
                    return $methodReflection->getReturnType();
                }
                $arg = $methodCall->args[0]->value;
                $type = Utility::getTypeFromVariable($arg, $methodReflection);
                return $type;
            break;

            default:
                throw \Exception('Unhandled method call: '.$name);
            break;
        }
        return $methodReflection->getReturnType();
    }
}
