<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PHPStan\Type\Type;
use PHPStan\Type\IterableType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\IterableTypeTrait;
use PHPStan\Type\NestedArrayItemType;
use PHPStan\Type\StaticResolvableType;

// Silverstripe
use Object;
use Config;
use DataObject;
use ContentController;

class DataListType extends ObjectType implements IterableType
{
    /** @var ObjectType */
    private $dataListType;

    /** @var Type */
    private $nestedType;

    public function __construct(string $dataListType, Type $nestedType) 
    {
        parent::__construct($dataListType);
        $this->dataListType = new ObjectType($dataListType);
        $this->nestedType = $nestedType;
    }

    public function getItemType(): Type
    {
        return $this->nestedType;
    }

    public function getNestedItemType(): NestedArrayItemType
    {
        return $this->nestedType;
    }

    public function describe(): string
    {
        return sprintf('%s(%s[])', $this->dataListType->getClass(), $this->nestedType->getClass());
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