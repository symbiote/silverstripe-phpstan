<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Broker\Broker;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;

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
    /** @var Broker */
    private $broker;

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
        }
        return false;
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        $type = null;
        if ($methodCall->var instanceof Variable) {
            $type = $scope->getVariableType($methodCall->var->name);
        } else {
            $type = $scope->getType($methodCall->var);
        }
        $className = '';
        if ($type === null) {
            return $methodReflection->getReturnType();
        }
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

        $name = $methodReflection->getName();
        switch ($name) {
            case 'dbObject':
                if (count($methodCall->args) === 0) {
                    return $methodReflection->getReturnType();
                }
                // Handle $this->dbObject('Field')
                $arg = $methodCall->args[0]->value;
                $value = '';
                if ($arg instanceof Variable) {
                    // Unhandled, cannot retrieve variable value even if set in this scope.
                    return $methodReflection->getReturnType();
                } else if ($arg instanceof String_) {
                    $value = $arg->value;
                }
                if (!$value) {
                    return $methodReflection->getReturnType();
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

            default:
                throw \Exception('Unhandled method call: '.$name);
            break;
        }
        return $methodReflection->getReturnType();
    }
}
