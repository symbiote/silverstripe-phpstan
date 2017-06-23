<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use \ReflectionClass;
use \ReflectionMethod;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Type\MixedType;

// Silverstripe Queued Jobs Module
use AbstractQueuedJob;

// todo(Jake): Move to its own directory (ie. seperate core functionality from module support)
class AbstractQueuedJobPropertyClassReflection implements \PHPStan\Reflection\PropertiesClassReflectionExtension {
    /** @var \PHPStan\Reflection\PropertyReflection[][] */
    private $properties = [];

    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        if (!$classReflection->isSubclassOf(AbstractQueuedJob::class)) {
            return false;
        }
        return true;
    }

    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        if (!$classReflection->isSubclassOf(AbstractQueuedJob::class)) {
            return false;
        }
        return new AbstractQueuedJobProperty($propertyName, $classReflection);
    }
}