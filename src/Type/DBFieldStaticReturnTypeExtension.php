<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Type;

use Symbiote\SilverstripePHPStan\ClassHelper;
use Symbiote\SilverstripePHPStan\Utility;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;

class DBFieldStaticReturnTypeExtension implements \PHPStan\Type\DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ClassHelper::DBField;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        $name = $methodReflection->getName();
        return $name === 'create_field';
    }

    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
    {
        $name = $methodReflection->getName();
        switch ($name) {
            case 'create_field':
                if (count($methodCall->args) === 0) {
                    return ParametersAcceptorSelector::selectFromArgs(
                        $scope,
                        $methodCall->args,
                        $methodReflection->getVariants()
                    )->getReturnType();
                }
                // Handle DBField::create_field('HTMLText', '<p>Value</p>')
                $arg = $methodCall->args[0]->value;
                $type = Utility::getTypeFromVariable($arg, $methodReflection);
                return $type;
        }
        $arg = $methodCall->args[0]->value;

        return $scope->getType($arg);
    }
}
