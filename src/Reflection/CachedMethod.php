<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Reflection;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Type;
use PHPStan\Type\MixedType;
use PHPStan\Reflection\Php\PhpMethodReflection;

class CachedMethod implements MethodReflection
{

    /**
     *
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var \PHPStan\Reflection\Php\PhpMethodReflection
     */
    private $methodReflection;

    /**
     *
     *
     * @var \PHPStan\Reflection\ClassReflection
     */
    private $declaringClass;

    public function __construct(PhpMethodReflection $methodReflection)
    {
        // Remove '_' from front of function
        $this->name = substr($methodReflection->getName(), 1);
        $this->methodReflection = $methodReflection;
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->methodReflection->getDeclaringClass();
    }

    public function getPrototype(): MethodReflection
    {
        return $this->methodReflection->getPrototype();
    }

    public function isStatic(): bool
    {
        return $this->methodReflection->isStatic();
    }

    public function getParameters(): array
    {
        return $this->methodReflection->getParameters();
    }

    public function isVariadic(): bool
    {
        return $this->methodReflection->isVariadic();
    }

    public function isPrivate(): bool
    {
        return $this->methodReflection->isPrivate();
    }

    public function isPublic(): bool
    {
        return $this->methodReflection->isPublic();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReturnType(): Type
    {
        return $this->methodReflection->getReturnType();
    }
}
