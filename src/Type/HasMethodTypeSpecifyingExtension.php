<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Accessory\HasMethodType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\MethodTypeSpecifyingExtension;
use PHPStan\Type\ObjectWithoutClassType;

class HasMethodTypeSpecifyingExtension implements MethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{
    /** @var TypeSpecifier */
    private $typeSpecifier;

    public function getClass(): string
    {
        return 'SilverStripe\\View\\ViewableData';
    }

    public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
    {
        $this->typeSpecifier = $typeSpecifier;
    }

    public function isMethodSupported(
        MethodReflection $methodReflection,
        MethodCall $node,
        TypeSpecifierContext $context
    ): bool
    {
        return $methodReflection->getName() === 'hasMethod'
            && $context->truthy()
            && count($node->args) >= 1;
    }

    public function specifyTypes(
        MethodReflection $methodReflection,
        MethodCall $node,
        Scope $scope,
        TypeSpecifierContext $context
    ): SpecifiedTypes
    {
        $methodNameType = $scope->getType($node->args[0]->value);
        if (!$methodNameType instanceof ConstantStringType) {
            return new SpecifiedTypes([], []);
        }
        return $this->typeSpecifier->create(
            $node->var,
            new HasMethodType($methodNameType->getValue()),
            $context
        );
    }
}
