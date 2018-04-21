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

// Silverstripe
use Injector;

class InjectorReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    /** @var string[] */
    private $methodNames = [
        'get' => '',
    ];

    public function getClass(): string
    {
        return Injector::class;
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
                if (count($methodCall->args[0]) === 0) {
                    return $methodReflection->getReturnType();
                }
                $arg = $methodCall->args[0]->value;
                $type = Utility::getClassFromInjectorVariable($arg, $methodReflection->getReturnType());
                return $type;
            break;

            default:
                throw \Exception('Unhandled method call: '.$name);
            break;
        }
        return $methodReflection->getReturnType();
    }
}
