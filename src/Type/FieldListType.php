<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Type;

use Symbiote\SilverstripePHPStan\ClassHelper;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Type\IterableTypeTrait;
use PHPStan\Type\StaticType;
use PHPStan\TrinaryLogic;
use PHPStan\Type\VerbosityLevel;

class FieldListType extends ObjectType
{

    /**
     * @var ObjectType
     */
    private $itemType;

    public function __construct(string $fieldListClassName)
    {
        parent::__construct($fieldListClassName);
        $this->itemType = new ObjectType(ClassHelper::FormField);
    }

    public function describe(VerbosityLevel $level): string
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
