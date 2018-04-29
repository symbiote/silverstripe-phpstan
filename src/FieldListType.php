<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Type\IterableTypeTrait;
use PHPStan\Type\StaticResolvableType;

// SilverStripe
use FormField;

class FieldListType extends ObjectType implements StaticResolvableType
{
    use IterableTypeTrait;

    public function __construct(string $fieldListClassName)
    {
        parent::__construct($fieldListClassName);
        $this->itemType = new ObjectType(FormField::class);
    }

    public function describe(): string
    {
        $fieldListClassName = count($this->getReferencedClasses()) === 1 ? $this->getReferencedClasses()[0] : '';
        $itemTypeClass = count($this->itemType->getReferencedClasses()) === 1 ? $this->itemType->getReferencedClasses()[0] : '';
        return sprintf('%s<%s>', $fieldListClassName, $itemTypeClass);
    }

    public function getItemType(): Type
    {
        return $this->itemType;
    }

    public function getIterableValueType(): Type
    {
        return $this->itemType;
    }

    public function resolveStatic(string $className): Type
    {
        return $this;
    }

    public function changeBaseClass(string $className): StaticResolvableType
    {
        return $this;
    }

    public function isDocumentableNatively(): bool
    {
        return true;
    }

    // IterableTrait

    public function canCallMethods(): bool
    {
        return true;
    }

    public function hasMethod(string $methodName): bool
    {
        return parent::hasMethod($methodName);
    }

    public function getMethod(string $methodName, Scope $scope): MethodReflection
    {
        return parent::getMethod($methodName, $scope);
    }

    public function isClonable(): bool
    {
        return true;
    }

    public function canAccessProperties(): bool
    {
        return parent::canAccessProperties();
    }

    public function hasProperty(string $propertyName): bool
    {
        return parent::hasProperty($propertyName);
    }

    public function getProperty(string $propertyName, Scope $scope): PropertyReflection
    {
        return parent::getProperty($propertyName, $scope);
    }
}
