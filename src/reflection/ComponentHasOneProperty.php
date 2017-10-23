<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\Type;
use PHPStan\Type\IntegerType;

class ComponentHasOneProperty implements PropertyReflection
{

    /** @var string */
    private $name;

    /** @var \PHPStan\Reflection\ClassReflection */
    private $declaringClass;

    /** @var  IntegerType */
    private $returnType;

    public function __construct(string $name, ClassReflection $declaringClass)
    {
        $this->name = $name;
        $this->declaringClass = $declaringClass;
        $this->returnType = new IntegerType;
    }

    public function getType(): Type {
        return $this->returnType;
    }
    
    public function getDeclaringClass(): ClassReflection {
        return $this->declaringClass;
    }

    public function isStatic(): bool {
        return false;
    }

    public function isPrivate(): bool {
        return false;
    }

    public function isPublic(): bool {
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
