<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests;

use ReflectionProperty;
use PHPStan\PhpDoc;
use PHPStan\Analyser\Scope;
use PHPStan\Cache\Cache;
use PHPStan\File\FileHelper;
use PHPStan\PhpDoc\PhpDocStringResolver;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Reflection\BrokerAwareExtension;

abstract class ResolverTest extends \PHPStan\Testing\TestCase
{
    /** @var \PHPStan\Broker\Broker */
    private $broker;

    protected function assertTypes(
        string $file,
        string $description,
        string $expression,
        array $dynamicMethodReturnTypeExtensions = [],
        array $dynamicStaticMethodReturnTypeExtensions = [],
        array $dynamicFunctionReturnTypeExtensions = [],
        string $evaluatedPointExpression = 'die;'
    ) {

        // NOTE(Jake): 2018-04-21
        //
        // If I don't do this, I get a class not found error.
        //
        // This is due to PHPStan using a classmap for loading files in its
        // 'data' folder.
        //
        include_once($file);

        // NOTE(Jake): 2018-04-21
        //
        // Taken from:
        // - phpstan\tests\PHPStan\Analyser\NodeScopeResolverTest.php
        //
        $this->processFile($file, function (\PhpParser\Node $node, Scope $scope) use ($description, $expression, $evaluatedPointExpression) {
            $printer = new \PhpParser\PrettyPrinter\Standard();
            $printedNode = $printer->prettyPrint([$node]);
            if ($printedNode === $evaluatedPointExpression) {
                /** @var \PhpParser\Node\Expr $expressionNode */
                $expressionNode = $this->getParser()->parseString(sprintf('<?php %s;', $expression))[0];
                $type = $scope->getType($expressionNode);
                $this->assertTypeDescribe(
                    $description,
                    $type->describe(),
                    sprintf('%s at %s', $expression, $evaluatedPointExpression)
                );
            }
        }, $dynamicMethodReturnTypeExtensions, $dynamicStaticMethodReturnTypeExtensions, $dynamicFunctionReturnTypeExtensions);
    }

    private function processFile(
        string $file,
        \Closure $callback,
        array $dynamicMethodReturnTypeExtensions = [],
        array $dynamicStaticMethodReturnTypeExtensions = [],
        array $dynamicFunctionReturnTypeExtensions = []
    ) {
        // NOTE(Jake): 2018-04-21
        //
        // Taken from:
        // - phpstan\tests\PHPStan\Analyser\NodeScopeResolverTest.php
        //
        $phpDocStringResolver = $this->getContainer()->getByType(PhpDocStringResolver::class);

        $printer = new \PhpParser\PrettyPrinter\Standard();
        $broker = $this->createBroker();

        // NOTE(Jake): 2018-04-22
        //
        // Hack in DynamicFunctionReturnType support
        //
        if ($dynamicFunctionReturnTypeExtensions) {
            $hack = $broker->getDynamicFunctionReturnTypeExtensions();
            $hack = array_merge($hack, $dynamicFunctionReturnTypeExtensions);
            foreach ($dynamicFunctionReturnTypeExtensions as $extension) {
                if ($extension instanceof BrokerAwareExtension) {
                    $extension->setBroker($broker);
                }
            }
            $refProperty = new \ReflectionProperty($broker, 'dynamicFunctionReturnTypeExtensions');
            $refProperty->setAccessible(true);
            $refProperty->setValue($broker, $hack);
        }

        $resolver = new NodeScopeResolver(
            $broker,
            $this->getParser(),
            $printer,
            new FileTypeMapper($this->getParser(), $phpDocStringResolver, $this->createMock(Cache::class)),
            new FileHelper('/'),
            true,
            true,
            [
                //\EarlyTermination\Foo::class => [
                //    'doFoo',
                //],
            ]
        );
        $broker = $this->createBroker(
            $dynamicMethodReturnTypeExtensions,
            $dynamicStaticMethodReturnTypeExtensions
        );

        // NOTE(Jake): 2018-04-22
        //
        // Hack in DynamicFunctionReturnType support
        // -DUPLICATE CODE-
        //
        if ($dynamicFunctionReturnTypeExtensions) {
            $hack = $broker->getDynamicFunctionReturnTypeExtensions();
            $hack = array_merge($hack, $dynamicFunctionReturnTypeExtensions);
            foreach ($dynamicFunctionReturnTypeExtensions as $extension) {
                if ($extension instanceof BrokerAwareExtension) {
                    $extension->setBroker($broker);
                }
            }
            $refProperty = new \ReflectionProperty($broker, 'dynamicFunctionReturnTypeExtensions');
            $refProperty->setAccessible(true);
            $refProperty->setValue($broker, $hack);
        }

        $resolver->processNodes(
            $this->getParser()->parseFile($file),
            new Scope(
                $broker,
                $printer,
                new TypeSpecifier($printer),
                $file
            ),
            $callback
        );
    }

    private function assertTypeDescribe(string $expectedDescription, string $actualDescription, string $label = '')
    {
        $this->assertSame(
            $expectedDescription,
            $actualDescription,
            $label
        );
    }
}
