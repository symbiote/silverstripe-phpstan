<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use PHPStan\Reflection\ParametersAcceptorWithPhpDocs;
use PhpParser\NodeAbstract;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Expr\Variable;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Reflection\MethodReflection;
use Exception;

// SilverStripe
use Config;
use Injector;

class Utility
{
    public static function getClassFromInjectorVariable(NodeAbstract $node, $defaultType): Type
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
            throw new Exception('Unhandled or invalid "class" data. Type passed:'.get_class($node));
        }
        $injectorInfo = Config::inst()->get(Injector::class, $label);
        if (!$injectorInfo) {
            return new ObjectType($label);
        }
        if (is_string($injectorInfo)) {
            return new ObjectType($injectorInfo);
        }
        if (is_array($injectorInfo) &&
            isset($injectorInfo['class'])) {
            return new ObjectType($injectorInfo['class']);
        }
        // ie. If only "properties" is set on a class/label, like the `RequestProcessor` class.
        return new ObjectType($label);
    }

    public static function getTypeFromVariable(NodeAbstract $node, ParametersAcceptorWithPhpDocs $methodOrFunctionReflection): ObjectType
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
        }
        if (!$class) {
            throw new Exception('Unhandled or invalid "class" data. Type passed:'.get_class($node));
        }
        return new ObjectType($class);
    }
}
