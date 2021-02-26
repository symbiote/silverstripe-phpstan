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
use PHPStan\Reflection\ParametersAcceptorSelector;
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
                    $type = Utility::getTypeFromInjectorVariable($arg, new ObjectType('SilverStripe\ORM\DataObject'));
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
        }
        // NOTE(mleutenegger): 2019-11-10
        // taken from https://github.com/phpstan/phpstan#dynamic-return-type-extensions
        if (count($methodCall->args) === 0) {
            return ParametersAcceptorSelector::selectFromArgs(
                $scope,
                $methodCall->args,
                $methodReflection->getVariants()
            )->getReturnType();
        }
        $arg = $methodCall->args[0]->value;

        return $scope->getType($arg);
    }
}
