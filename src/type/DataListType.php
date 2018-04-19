<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PHPStan\Type\Type;
use PHPStan\Type\IterableType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\IterableTypeTrait;
use PHPStan\Type\NestedArrayItemType;
use PHPStan\Type\StaticResolvableType;

class DataListType extends ObjectType implements StaticResolvableType
{
    use IterableTypeTrait;

    /** @var ObjectType */
    private $dataListType;

    public function __construct(string $dataListClassName, Type $itemType)
    {
        parent::__construct($dataListClassName);
        $this->dataListType = new ObjectType($dataListClassName);
        $this->itemType = $itemType;
    }

    public function describe(): string
    {
        $dataListTypeClass = count($this->dataListType->getReferencedClasses()) === 1 ? $this->dataListType->getReferencedClasses()[0] : '';
        $itemTypeClass = count($this->itemType->getReferencedClasses()) === 1 ? $this->itemType->getReferencedClasses()[0] : '';
        return sprintf('%s<%s[]>', $dataListTypeClass, $itemTypeClass);
    }

    public function getDataListType(): ObjectType
    {
        return $this->dataListType;
    }

    public function getIterableValueType(): Type
    {
        return $this->itemType;
    }

    public function getReferencedClasses(): array
    {
        return $this->dataListType->getReferencedClasses();
    }

    public function resolveStatic(string $className): Type
    {
        return $this;
    }

    public function changeBaseClass(string $className): StaticResolvableType
    {
        return $this;
    }

    public function canAccessProperties(): bool
    {
        return true;
    }

    public function canCallMethods(): bool
    {
        return true;
    }

    public function isDocumentableNatively(): bool
    {
        return true;
    }
}
