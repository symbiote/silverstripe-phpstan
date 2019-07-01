<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Type;

use Symbiote\SilverstripePHPStan\ClassHelper;
use Symbiote\SilverstripePHPStan\Utility;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;

class DataObjectGetStaticReturnTypeExtension implements \PHPStan\Type\DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ClassHelper::DataObject;
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
                if (count($methodCall->args) > 0) {
                    // Handle DataObject::get('Page')
                    $arg = $methodCall->args[0];
                    $type = Utility::getTypeFromVariable($arg, $methodReflection);
                    return new DataListType(ClassHelper::DataList, $type);
                }
                // Handle Page::get() / self::get()
                $callerClass = $methodCall->class->toString();
                if ($callerClass === 'static') {
                    return Utility::getMethodReturnType($methodReflection);
                }
                if ($callerClass === 'self') {
                    $callerClass = $scope->getClassReflection()->getName();
                }
                return new DataListType(ClassHelper::DataList, new ObjectType($callerClass));
            break;

            case 'get_one':
            case 'get_by_id':
                if (count($methodCall->args) > 0) {
                    // Handle DataObject::get_one('Page')
                    $arg = $methodCall->args[0];
                    $type = Utility::getTypeFromVariable($arg, $methodReflection);
                    return $type;
                }
                // Handle Page::get() / self::get()
                $callerClass = $methodCall->class->toString();
                if ($callerClass === 'static') {
                    return Utility::getMethodReturnType($methodReflection);
                }
                if ($callerClass === 'self') {
                    $callerClass = $scope->getClassReflection()->getName();
                }
                return new ObjectType($callerClass);
            break;
        }
        return $methodReflection->getReturnType();
    }
}
