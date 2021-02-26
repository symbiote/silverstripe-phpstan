<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Type;

use LogicException;
use Symbiote\SilverstripePHPStan\ClassHelper;
use Symbiote\SilverstripePHPStan\Utility;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;

class InjectorReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    /** @var string[] */
    private $methodNames = [
        'get' => '',
    ];

    public function getClass(): string
    {
        return ClassHelper::Injector;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return isset($this->methodNames[$methodReflection->getName()]);
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        $name = $methodReflection->getName();
        switch ($name) {
            case 'get':
                if (count($methodCall->args) === 0) {
                    return Utility::getMethodReturnType($methodReflection);
                }
                $arg = $methodCall->args[0]->value;
                $type = Utility::getTypeFromInjectorVariable(
                    $arg,
                    Utility::getMethodReturnType($methodReflection)
                );
                return $type;

            default:
                throw new LogicException('Unhandled method call: '.$name);
        }
    }
}
