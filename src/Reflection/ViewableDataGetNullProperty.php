<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Reflection;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\Type;
use PHPStan\Type\StringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\NullType;

class ViewableDataGetNullProperty implements PropertyReflection
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
     * @var NullType
     */
    private $returnType;

    public function __construct(string $name, ClassReflection $declaringClass)
    {
        $this->name = $name;
        $this->declaringClass = $declaringClass;
        $this->returnType = new NullType;
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
