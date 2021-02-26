<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Tests;

use ReflectionProperty;
use PHPStan\PhpDoc;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Cache\Cache;
use PHPStan\File\FileHelper;
use PHPStan\PhpDoc\PhpDocStringResolver;
use PHPStan\PhpDoc\PhpDocInheritanceResolver;
use PHPStan\PhpDoc\PhpDocNodeResolver;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Type\VerbosityLevel;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Reflection\ReflectionProvider\DirectReflectionProviderProvider;
use PHPStan\File\FuzzyRelativePathHelper;
use PHPStan\File\NullRelativePathHelper;
use PHPStan\Broker\AnonymousClassNameHelper;
use PHPStan\Rules\Properties\PropertyReflectionFinder;
use PHPStan\Node\VirtualNode;
use PHPStan\Php\PhpVersion;

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
        $this->processFile(
            $file,
            function (\PhpParser\Node $node, Scope $scope) use ($description, $expression, $evaluatedPointExpression) {
                if ($node instanceof VirtualNode) {
                    return;
                }
                $printer = new \PhpParser\PrettyPrinter\Standard();
                $printedNode = $printer->prettyPrint([$node]);
                if ($printedNode === $evaluatedPointExpression) {
                    /** @var \PhpParser\Node\Stmt\Expression $expressionNode */
                    $expressionNode = $this->getParser()->parseString(sprintf('<?php %s;', $expression))[0];
                    $type = $scope->getType($expressionNode->expr);
                    $this->assertTypeDescribe(
                        $description,
                        $type->describe(VerbosityLevel::precise()),
                        sprintf('%s at %s', $expression, $evaluatedPointExpression)
                    );
                }
            },
            $dynamicMethodReturnTypeExtensions,
            $dynamicStaticMethodReturnTypeExtensions
        );
    }

    private function processFile(
        string $file,
        \Closure $callback,
        array $dynamicMethodReturnTypeExtensions = [],
        array $dynamicStaticMethodReturnTypeExtensions = []
    ) {
        // NOTE(Jake): 2018-04-21
        //
        // Taken from:
        // - phpstan\tests\PHPStan\Analyser\NodeScopeResolverTest.php
        //
        $phpDocStringResolver = $this->getContainer()->getByType(PhpDocStringResolver::class);
        $phpDocNodeResolver = $this->getContainer()->getByType(PhpDocNodeResolver::class);

        $printer = new \PhpParser\PrettyPrinter\Standard();
        $broker = $this->createBroker();

        $workingDirectory = __DIR__;
        $relativePathHelper = new FuzzyRelativePathHelper(new NullRelativePathHelper(), $workingDirectory, [], DIRECTORY_SEPARATOR);
        $anonymousClassNameHelper = new AnonymousClassNameHelper(new FileHelper($workingDirectory), $relativePathHelper);

        $typeSpecifier = $this->createTypeSpecifier(
            $printer,
            $broker,
            [],
            []
        );

        $fileHelper = new FileHelper($workingDirectory);

        $fileTypeMapper =new FileTypeMapper(
            new DirectReflectionProviderProvider($broker),
            $this->getParser(),
            $phpDocStringResolver,
            $phpDocNodeResolver,
            $this->createMock(Cache::class),
            $anonymousClassNameHelper
        );

        $phpDocInheritanceResolver = new PhpDocInheritanceResolver($fileTypeMapper);

        $resolver = new NodeScopeResolver(
            $broker,
            self::getReflectors()[0],
            $this->getClassReflectionExtensionRegistryProvider(),
            $this->getParser(),
            $fileTypeMapper,
            self::getContainer()->getByType(PhpVersion::class),
            $phpDocInheritanceResolver,
            $fileHelper,
            $typeSpecifier,
            true,
            true,
            true,
            [],
            ['baz']
        );

        $broker = $this->createBroker(
            $dynamicMethodReturnTypeExtensions,
            $dynamicStaticMethodReturnTypeExtensions
        );

        $scopeFactory = $this->createScopeFactory($broker, $typeSpecifier);
        $scope = $scopeFactory->create(ScopeContext::create($file));
        $resolver->processNodes(
            $this->getParser()->parseFile($file),
            $scope,
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
