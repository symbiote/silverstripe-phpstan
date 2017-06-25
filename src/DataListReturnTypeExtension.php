<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PHPStanVendor\PhpParser\Node\Expr\MethodCall;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;

// Silverstripe
use DataList;

class DataListReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension {
    public static function getClass(): string
    {
        return DataList::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'filter';
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        //if ($scope->getFile() === 'D:\wamp\www\Work\DPCNSW\dpc-nsw\code\pages\Page.php') {
            //var_dump(get_class($methodReflection->getReturnType())); exit;
        //}
        return $methodReflection->getReturnType();
    }
}
