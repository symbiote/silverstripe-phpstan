<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests;

use PHPStan\PhpDoc;
use PHPStan\Analyser\Scope;
use PHPStan\Cache\Cache;
use PHPStan\File\FileHelper;
use PHPStan\PhpDoc\PhpDocStringResolver;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Reflection\BrokerAwareExtension;

// SilverStripe
use DataObject;
use DataList;
use SiteTree;
use File;
use Cookie_Backend;
use CookieJar;
use Injector;
use MySQLDatabase;
use MySQLPDODatabase;

final class DataObjectGetStaticReturnTypeExtensionTest extends \PHPStan\Testing\TestCase
{
    /** @var \PHPStan\Broker\Broker */
    private $broker;

    public function dataDynamicMethodReturnTypeExtensions(): array
    {
        return [
            // Test `SiteTree::get()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('%s::get()', SiteTree::class),
            ],
            // DataObject::get('SiteTree')
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('%s::get("%s")', DataObject::class, SiteTree::class),
            ],
            // DataObject::get(SiteTree::class)
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('%s::get(%s::class)', DataObject::class, SiteTree::class),
            ],
            // SiteTree::get_one()
            [
                SiteTree::class,
                sprintf('%s::get_one()', SiteTree::class),
            ],
            // DataObject::get_one('SiteTree')
            [
                SiteTree::class,
                sprintf('%s::get_one("%s")', DataObject::class, SiteTree::class),
            ],
            // DataObject::get_one(SiteTree::class)
            [
                sprintf(SiteTree::class),
                sprintf('%s::get_one(%s::class)', DataObject::class, SiteTree::class),
            ],
            // Injector::inst()->get(File::class)
            [
                sprintf(File::class),
                sprintf('%s::inst()->get(%s::class)', Injector::class, File::class),
            ],
            // Test `Injector::inst()->get('Cookie_Backend)` returns `CookieJar` (uses direct value in YML, SS 3.6.X)
            [
                sprintf('%s', CookieJar::class),
                sprintf('%s::inst()->get(%s::class)', Injector::class, Cookie_Backend::class),
            ],
            // Test `Injector::inst()->get("MySQLPDODatabase")` returns `MySQLDatabase` (uses "class" array in YML, SS 3.6.X)
            [
                sprintf('%s', MySQLDatabase::class),
                sprintf('%s::inst()->get(%s::class)', Injector::class, MySQLPDODatabase::class),
            ],
            // 
            [
                sprintf('%s', Object::class),
                sprintf('class ExtensionTest extends \DataExtension { public function Test() { return $this->getOwner(); } }'),
            ]
        ];
    }

    /**
     * @dataProvider dataDynamicMethodReturnTypeExtensions
     * @param string $description
     * @param string $expression
     */
    public function testDynamicMethodReturnTypeExtensions(
        string $description,
        string $expression
    )
    {
        $dynamicMethodReturnTypeExtensions = [
            new \SilbinaryWolf\SilverstripePHPStan\InjectorReturnTypeExtension(),
        ];
        $dynamicStaticMethodReturnTypeExtensions = [
            new \SilbinaryWolf\SilverstripePHPStan\DataObjectGetStaticReturnTypeExtension(),
        ];
        $this->assertTypes(
            __DIR__ . '/data/data-object-dynamic-method-return-types.php',
            $description,
            $expression,
            $dynamicMethodReturnTypeExtensions, 
            $dynamicStaticMethodReturnTypeExtensions
        );
    }

    public function dataFunctionReturnTypeExtensions(): array
    {
        return [
            // Test `singleton('File)` returns `File`
            [
                sprintf('%s', File::class),
                sprintf('singleton("%s")', File::class),
            ],
            // Test `singleton("Cookie_Backend")` returns `CookieJar` (uses direct value in YML, SS 3.6.X)
            [
                sprintf('%s', CookieJar::class),
                sprintf('singleton("%s")', Cookie_Backend::class),
            ],
            // Test `singleton("MySQLPDODatabase")` returns `MySQLDatabase` (uses "class" array in YML, SS 3.6.X)
            [
                sprintf('%s', MySQLDatabase::class),
                sprintf('singleton("%s")', MySQLPDODatabase::class),
            ]
        ];
    }

    /**
     * @dataProvider dataFunctionReturnTypeExtensions
     * @param string $description
     * @param string $expression
     */
    public function testFunctionReturnTypeExtensions(
        string $description,
        string $expression
    )
    {
        $dynamicFunctionReturnTypeExtensions = [
            new \SilbinaryWolf\SilverstripePHPStan\SingletonReturnTypeExtension(),
        ];
        $this->assertTypes(
            __DIR__ . '/data/data-object-dynamic-method-return-types.php',
            $description,
            $expression,
            [],
            [],
            $dynamicFunctionReturnTypeExtensions
        );
    }

    private function assertTypes(
        string $file,
        string $description,
        string $expression,
        array $dynamicMethodReturnTypeExtensions = [],
        array $dynamicStaticMethodReturnTypeExtensions = [],
        array $dynamicFunctionReturnTypeExtensions = [],
        string $evaluatedPointExpression = 'die;'
    )
    {

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
    )
    {
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
                \EarlyTermination\Foo::class => [
                    'doFoo',
                ],
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
