<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PHPStanVendor\PhpParser\Node\Expr\StaticCall;
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
use DataObject;

class DataObjectGetStaticReturnTypeExtension implements \PHPStan\Type\DynamicStaticMethodReturnTypeExtension
{
    public static function getClass(): string
    {
        return DataObject::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        $name = $methodReflection->getName();
        return $name === 'get' ||
               $name === 'get_one' ||
               $name === 'get_by_id';
    }

    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
    {
        $name = $methodReflection->getName();
        switch ($name) {
            case 'get':
                if (count($methodCall->args) !== 0) {
                    // Handle DataObject::get('Page')
                    $arg = $methodCall->args[0];
                    if ($arg instanceof String_) {
                        return new DataListType(DataList::class, new ObjectType($arg->value));
                    }
                    return $methodReflection->getReturnType();
                }
                // Handle Page::get() / self::get()
                $callerClass = $methodCall->class->toString();
                if ($callerClass === 'static') {
                    return $methodReflection->getReturnType();
                }
                if ($callerClass === 'self') {
                    $callerClass = $scope->getClassReflection()->getName();
                }
                return new DataListType(DataList::class, new ObjectType($callerClass));
            break;

            case 'get_one':
            case 'get_by_id':
                if (count($methodCall->args) === 0) {
                    return $methodReflection->getReturnType();
                }
                $arg = $methodCall->args[0]->value;
                if ($arg instanceof String_) {
                    return new ObjectType($arg->value);
                }
                /*if ($arg instanceof PropertyFetch) {
                    $vars = $scope->getVariableTypes();
                   var_dump($vars);
                    var_dump($arg);  
                }*/
                return $methodReflection->getReturnType();
            break;
        }
        return $methodReflection->getReturnType();
    }
}
