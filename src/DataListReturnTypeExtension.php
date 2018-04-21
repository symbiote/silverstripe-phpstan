<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

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

// Silverstripe
use DataList;

class DataListReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return DataList::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        $name = $methodReflection->getName();
        switch ($name) {
            // DataList
            case 'filter':
            case 'reverse':
            case 'where':
            case 'whereAny':
            case 'innerJoin':
            case 'sort':
            case 'limit':
            case 'exclude':
            case 'setDataQueryParam':
            case 'alterDataQuery':
            case 'setQueriedColumns':
            case 'byIDs':
            case 'addMany':
            case 'removeMany':
            case 'removeByFilter':
            case 'removeAll':
                // int[]
            case 'getIDList':
                // DataObject[]
            case 'toArray':
                // DataObject
            case 'find':
            case 'byID':
            case 'first':
            case 'last':
                return true;
            break;

            /*case 'min':
            case 'max':
            case 'avg':
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

        // NOTE(Jake): 2018-04-21
        // Said it could be simplified to this:
        // https://github.com/phpstan/phpstan/issues/350#issuecomment-339159006
        //
        $type = $scope->getType($methodCall->var);

        switch ($name) {
            // DataList
            case 'filter':
            case 'reverse':
            case 'where':
            case 'whereAny':
            case 'innerJoin':
            case 'sort':
            case 'limit':
            case 'exclude':
            case 'setDataQueryParam':
            case 'alterDataQuery':
            case 'setQueriedColumns':
            case 'byIDs':
            case 'addMany':
            case 'removeMany':
            case 'removeByFilter':
            case 'removeAll':
                return $type;
            break;

            case 'getIDList':
                return new ArrayType(new IntegerType, new IntegerType);
            break;

            // DataObject[]
            case 'toArray':
                return new ArrayType(new IntegerType, $type->getItemType());
            break;

            // DataObject
            case 'find':
            case 'byID':
            case 'first':
            case 'last':
                return $type->getIterableValueType();
            break;

            default:
                throw Exception('Unhandled method call: '.$name);
            break;
        }
        return $methodReflection->getReturnType();
    }
}
