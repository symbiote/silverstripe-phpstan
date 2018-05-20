<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use Exception;
use PhpParser\NodeAbstract;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\ArrayDimFetch;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\FloatType;
use PHPStan\Type\MixedType;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorWithPhpDocs;

class Utility
{
    public static function get_primitive_from_dbfield($className): Type
    {
        $returnType = null;
        if (is_a($className, ClassHelper::StringField, true)) {
            $returnType = new StringType;
        } elseif (is_a($className, 'Int', true) || // 'Int' == Support SS 3.5 and below
            is_a($className, ClassHelper::DBInt, true)) {
            $returnType = new IntegerType;
        } elseif (is_a($className, 'Float', true) || // 'Int' == Support SS 3.5 and below
            is_a($className, ClassHelper::DBFloat, true)) {
            $returnType = new FloatType;
        } else {
            $returnType = new MixedType;
        }
        return $returnType;
    }

    public static function getTypeFromInjectorVariable(NodeAbstract $node, $defaultType): Type
    {
        $label = '';
        if ($node instanceof Arg) {
            $node = $node->value;
        }
        if ($node instanceof String_) {
            // Handle string: 'HomePage'
            $label = $node->value;
        } else if ($node instanceof ClassConstFetch) {
            // Handle type: 'HomePage::class'
            $label = (string)$node->class;
        } else if ($node instanceof PropertyFetch) {
            // Handle passing of: '$this->modelClass' in ModelAdmin to 'singleton'
            return $defaultType;
        } else if ($node instanceof Variable) {
            // NOTE(Jake): 2018-04-21
            //
            // If we pass in scope, we can get the variable type:
            // - $scope->getVariableType($node->name)
            //
            // However, it seems that PHPStan does not retain constant
            // strings / values in scope, so we just need to rely on
            // what the method returns in its type hinting.
            //
            return $defaultType;
        }
        if (!$label) {
            throw new Exception(__FUNCTION__.': Unhandled or invalid "class" data. Type passed:'.get_class($node));
        }
        return self::getClassFromInjectorString($label);
    }

    public static function getClassFromInjectorString($classNameOrLabel): ObjectType
    {
        $injectorInfo = ConfigHelper::get(ClassHelper::Injector, $classNameOrLabel);
        if (!$injectorInfo) {
            return new ObjectType($classNameOrLabel);
        }
        if (is_string($injectorInfo)) {
            return new ObjectType($injectorInfo);
        }
        if (is_array($injectorInfo) &&
            isset($injectorInfo['class'])) {
            return new ObjectType($injectorInfo['class']);
        }
        // NOTE(Jake): 2018-05-05
        //
        // If only "properties" is set on a class/label, like the `RequestProcessor` class.
        // Then we simply use the original class name passed in. (No override is configured)
        //
        return new ObjectType($classNameOrLabel);
    }

    public static function getTypeFromVariable(NodeAbstract $node, ParametersAcceptorWithPhpDocs $methodOrFunctionReflection): Type
    {
        $class = '';
        if ($node instanceof Arg) {
            $node = $node->value;
        }

        if ($node instanceof String_) {
            // Handle string: 'HomePage'
            $class = $node->value;
        } else if ($node instanceof ClassConstFetch) {
            // Handle type: 'HomePage::class'
            $class = (string)$node->class;
        } else if ($node instanceof Variable) {
            if ($node->name === 'this') {
                // NOTE(Jake): 2018-04-25
                //
                // We might want to handle $this better.
                // This would require having `Scope`
                //
            }
            // NOTE(Jake): 2018-04-21
            //
            // If we pass in scope, we can get the variable type:
            // - $scope->getVariableType($node->name)
            //
            // However, it seems that PHPStan does not retain constant
            // strings / values in scope, so we just need to rely on
            // what the method returns in its type hinting.
            //
            return $methodOrFunctionReflection->getReturnType();
        } else if ($node instanceof ArrayDimFetch) {
            // NOTE(Jake): 2018-05-19
            //
            // If we pass in scope, we can get the variable type:
            // - $scope->getVariableType($node->name)
            //
            // However, it seems that PHPStan does not retain constant
            // strings / values in scope, so we just need to rely on
            // what the method returns in its type hinting.
            //
            return $methodOrFunctionReflection->getReturnType();
        }
        if (!$class) {
            throw new Exception(__FUNCTION__.':Unhandled or invalid "class" data. Type passed:'.get_class($node));
        }
        return new ObjectType($class);
    }
}
