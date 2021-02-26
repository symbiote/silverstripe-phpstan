<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Type;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Type\IterableTypeTrait;
use PHPStan\TrinaryLogic;
use PHPStan\Type\VerbosityLevel;

class DataListType extends ObjectType
{
    /** @var Type */
    private $itemType;

    public function __construct(string $dataListClassName, Type $itemType)
    {
        parent::__construct($dataListClassName);
        $this->itemType = $itemType;
    }

    public function describe(VerbosityLevel $level): string
    {
        $dataListTypeClass = count($this->getReferencedClasses()) === 1 ? $this->getReferencedClasses()[0] : '';
        $itemTypeClass = count($this->itemType->getReferencedClasses()) === 1 ? $this->itemType->getReferencedClasses()[0] : '';
        return sprintf('%s<%s>', $dataListTypeClass, $itemTypeClass);
    }

    public function getItemType(): Type
    {
        return $this->itemType;
    }

    public function getIterableValueType(): Type
    {
        return $this->itemType;
    }

    public function isDocumentableNatively(): bool
    {
        return true;
    }

    // IterableTrait

    public function canCallMethods(): TrinaryLogic
    {
        return TrinaryLogic::createYes();
    }

    public function isClonable(): TrinaryLogic
    {
        return TrinaryLogic::createYes();
    }
}
