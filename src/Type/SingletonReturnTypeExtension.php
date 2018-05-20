<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Type;

use SilbinaryWolf\SilverstripePHPStan\ClassHelper;
use SilbinaryWolf\SilverstripePHPStan\Utility;
use PHPStan\Type\Type;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;

class SingletonReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{
    /** @var string[] */
    private $functionNames = [
        'singleton' => '',
    ];

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return isset($this->functionNames[strtolower($functionReflection->getName())]);
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $name = $functionReflection->getName();
        switch ($name) {
            case 'singleton':
                if (count($functionCall->args) === 0) {
                    return $functionReflection->getReturnType();
                }
                // Handle singleton('HTMLText')
                $arg = $functionCall->args[0]->value;
                $type = Utility::getTypeFromInjectorVariable($arg, $functionReflection->getReturnType());
                return $type;
            break;
        }
        return $functionReflection->getReturnType();
    }
}
