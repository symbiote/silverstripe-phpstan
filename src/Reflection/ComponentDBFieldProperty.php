<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Reflection;

use Symbiote\SilverstripePHPStan\Utility;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;

class ComponentDBFieldProperty implements PropertyReflection
{

    /**
     *
     *
     * @var string
     */
    private $name;

    /**
     *
     *
     * @var \PHPStan\Reflection\ClassReflection
     */
    private $declaringClass;

    /**
     *
     *
     * @var Type
     */
    private $returnType;

    public function __construct(string $name, ClassReflection $declaringClass, ObjectType $type)
    {
        $this->name = $name;
        $this->declaringClass = $declaringClass;

        // Transform ObjectType 'DBInt' to 'IntegerType' for property access
        $className = $type->getClassName();
        $this->returnType = Utility::get_primitive_from_dbfield($className);
    }

    public function getType(): Type
    {
        return $this->returnType;
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->declaringClass;
    }

    public function isStatic(): bool
    {
        return false;
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function isWritable(): bool
    {
        return true;
    }
}
