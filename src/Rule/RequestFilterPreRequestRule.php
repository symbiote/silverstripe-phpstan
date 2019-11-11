<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Rule;

use Symbiote\SilverstripePHPStan\ClassHelper;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Constant\ConstantBooleanType;

class RequestFilterPreRequestRule implements \PHPStan\Rules\Rule
{
    public function getNodeType(): string
    {
        return Return_::class;
    }

    /**
     * @param Return_ $node
     * @param Scope $scope
     * @return string[]
     */
    public function processNode(\PhpParser\Node $node, Scope $scope): array
    {
        $classRefl = $scope->getClassReflection();
        if (!$classRefl) {
            return [];
        }

        $className = $classRefl->getName();
        if (!is_a($className, ClassHelper::RequestFilter, true)) {
            return [];
        }
        $functionName = $scope->getFunctionName();
        if ($functionName !== 'preRequest') {
            return [];
        }
        if ($node->expr === null) {
            return [];
        }
        $returnType = $scope->filterByFalseyValue($node->expr)->getType($node->expr);
        if ($returnType instanceof ConstantBooleanType) {
            // NOTE(Jake): 2018-04-25
            //
            // Added for SS 3.X. This might not be true in SS 4.0
            //
            return [
                sprintf(
                    '%s::preRequest() should not return false as this will cause an uncaught "Invalid Request" exception to be thrown by the SilverStripe framework. (returning "null" will not cause this problem)',
                    ClassHelper::RequestFilter
                ),
            ];
        }
        return [];
    }
}
