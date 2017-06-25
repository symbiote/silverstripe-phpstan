<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\Type;
use PHPStan\Type\StringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;

class ComponentDBFieldProperty implements PropertyReflection
{

    /** @var string */
    private $name;

    /** @var \PHPStan\Reflection\ClassReflection */
    private $declaringClass;

    /** @var  IntegerType */
    private $returnType;

    public function __construct(string $name, ClassReflection $declaringClass, string $type)
    {
        $this->name = $name;
        $this->declaringClass = $declaringClass;

        switch ($type) {
            case 'Varchar':
            case 'HTMLVarchar':
            case 'HTMLText':
                $this->returnType = new StringType;
            break;

            case 'Int':
            case 'DBInt':
                $this->returnType = new IntegerType;
            break;

            default:
                $this->returnType = new MixedType;
            break;
        }
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
}
