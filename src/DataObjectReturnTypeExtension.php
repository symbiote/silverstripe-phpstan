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

class DataObjectReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ClassHelper::DataObject;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        $name = $methodReflection->getName();
        switch ($name) {
            case 'getCMSFields':
                return true;
            break;

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
        $type = $scope->getType($methodCall->var);

        $name = $methodReflection->getName();
        switch ($name) {
            case 'getCMSFields':
                // todo(Jake): 2018-04-29
                //
                // This is very incomplete.
                //
                // Look into determining the values of a FieldList based on scaffolding
                // and SiteTree defaults. This is so `GridField` and other FormField
                // subclasses can be reasoned about.
                //
                // A current blocker in PHPStan 0.9.2 is that `parent::getCMSFields()`
                // won't work with `DataObjectReturnTypeExtension` but if I call it
                // directly from another function like `$this->getCMSFields()`, this will
                // execute.
                //
                $objectType = $methodReflection->getReturnType();
                if (!($objectType instanceof ObjectType)) {
                    throw new Exception('Unexpected type: '.get_class($objectType).', expected ObjectType');
                }
                $className = $objectType->getClassName();
                return new FieldListType($className);
            break;

            case 'dbObject':
                $className = '';
                if ($type instanceof StaticType) {
                    if (count($type->getReferencedClasses()) === 1) {
                        $className = $type->getReferencedClasses()[0];
                    }
                } else if ($type instanceof ObjectType) {
                    $className = $type->getClassName();
                }
                if (!$className) {
                    throw new Exception('Unhandled type: '.get_class($type));
                    //return $methodReflection->getReturnType();
                }
                if (count($methodCall->args) === 0) {
                    return $methodReflection->getReturnType();
                }
                // Handle $this->dbObject('Field')
                $arg = $methodCall->args[0]->value;
                $fieldName = '';
                if ($arg instanceof Variable) {
                    // Unhandled, cannot retrieve variable value even if set in this scope.
                    return $methodReflection->getReturnType();
                } else if ($arg instanceof ClassConstFetch) {
                    // Handle "SiteTree::class" constant
                    $fieldName = (string)$arg->class;
                } else if ($arg instanceof String_) {
                    $fieldName = $arg->value;
                }
                if (!$fieldName) {
                    throw new Exception('Mishandled "newClassInstance" call.');
                    //return $methodReflection->getReturnType();
                }
                $dbFields = ConfigHelper::get_db($className);
                if (!isset($dbFields[$fieldName])) {
                    return $methodReflection->getReturnType();
                }
                $dbFieldType = $dbFields[$fieldName];
                if (!$dbFieldType) {
                    return $methodReflection->getReturnType();
                }
                return $dbFieldType;
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
                throw new Exception('Unhandled method call: '.$name);
            break;
        }
        return $methodReflection->getReturnType();
    }
}
