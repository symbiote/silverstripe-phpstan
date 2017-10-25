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
        return sprintf('%s<%s[]>', $this->dataListType->getClass(), $this->itemType->getClass());
    }

    public function getDataListType(): ObjectType
    {
        return $this->dataListType;
    }

    public function getIterableValueType(): Type
    {
        return $this->itemType;
    }

    public function getClass(): string
    {
        return $this->dataListType->getClass();
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
