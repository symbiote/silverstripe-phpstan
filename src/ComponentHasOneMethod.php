<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Type;
use PHPStan\Type\ArrayType;

class ComponentHasOneMethod implements MethodReflection
{

    /** @var string */
    private $name;

    /** @var \PHPStan\Reflection\ClassReflection */
    private $declaringClass;

    /** @var  ArrayType */
    private $returnType;

    public function __construct(string $name, ClassReflection $declaringClass, Type $type)
    {
        $this->name = $name;
        $this->declaringClass = $declaringClass;
        $this->returnType = $type;
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->declaringClass;
    }

    public function getPrototype(): MethodReflection
    {
        return $this;
    }

    public function isStatic(): bool
    {
        return false;
    }

    public function getParameters(): array
    {
        return [];
    }

    public function isVariadic(): bool
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getReturnType(): Type
    {
        return $this->returnType;
    }
}
