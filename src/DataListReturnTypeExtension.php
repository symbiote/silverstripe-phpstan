<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PHPStanVendor\PhpParser\Node\Expr\MethodCall;
use PHPStanVendor\PhpParser\Node\Expr\PropertyFetch;
use PHPStanVendor\PhpParser\Node\Expr\Variable;
use PHPStanVendor\PhpParser\Node\Scalar\String_;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;

// Silverstripe
use DataList;

class DataListReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension {
    public static function getClass(): string
    {
        return DataList::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        $name = $methodReflection->getName();
        switch ($name) {
            // DataList
            case 'filter':
            case 'where':
            case 'whereAny':
            case 'innerJoin':
            case 'sort':
            case 'limit':
            case 'exclude':
            case 'min':
            case 'setDataQueryParam':
            case 'alterDataQuery':

            // DataObject
            case 'find':
            case 'byID':
            case 'first':
            case 'last':
                return true;
            break;

            /*case 'toArray':
            case 'dataClass':
            case 'column':
            case 'map':
            case 'count':
                // no-op
            break;

            default:
                // Debug: Find unused method names
                //var_dump($name); exit;
            break;*/
        }
        return false;
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        $name = $methodReflection->getName();

        $type = null;
        if ($methodCall->var instanceof Variable) {
            $type = $scope->getVariableType($methodCall->var->name);
        } else {
            $type = $scope->getType($methodCall->var);
        }

        if (!$type || !($type instanceof DataListType)) {
            return $methodReflection->getReturnType();
        }
        switch ($name) {
            // DataList
            case 'filter':
            case 'where':
            case 'whereAny':
            case 'innerJoin':
            case 'sort':
            case 'limit':
            case 'exclude':
            case 'min':
            case 'setDataQueryParam':
            case 'alterDataQuery':
                if ($type && $type instanceof DataListType) {
                    return $type;
                }
            break;

            case 'find':
            case 'byID':
            case 'first':
            case 'last':
                if ($type && $type instanceof DataListType) {
                    return $type->getItemType();
                }
            break;

            default:
                throw Exception('Unhandled method call: '.$name);
            break;
        }
        return $methodReflection->getReturnType();
    }
}
