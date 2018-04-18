<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\Expr;
use PhpParser\Node\Scalar\String_;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;

// Silverstripe
use DBField;

class DBFieldStaticReturnTypeExtension implements \PHPStan\Type\DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return DBField::class;
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
                    return $methodReflection->getReturnType();
                }
                // Handle DBField::create_field('HTMLText', '<p>Value</p>')
                $arg = $methodCall->args[0]->value;
                $value = '';
                if ($arg instanceof Variable) {
                    // Unhandled, cannot retrieve variable value even if set in this scope.
                    return $methodReflection->getReturnType();
                }
                if ($arg instanceof String_) {
                    $value = $arg->value;
                }
                if (!$value) {
                    // If value is empty
                    return $methodReflection->getReturnType();
                }
                return new ObjectType($value);
            break;
        }
        return $methodReflection->getReturnType();
    }
}
