<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests;

// SilverStripe
use File;
use Injector;
use CookieJar;
use Cookie_Backend;
use MySQLDatabase;
use MySQLPDODatabase;

// NOTE(Jake): 2018-04-05
//
// Workaround Composer Autoloader / PHPUnit not working as I expect.
//
include_once(dirname(__FILE__).'/ResolverTest.php');

class InjectorReturnTypeExtensionTest extends ResolverTest
{
    public function dataDynamicMethodReturnTypeExtensions(): array
    {
        return [
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
            /*[
                sprintf('%s', Object::class),
                sprintf('class ExtensionTest extends \DataExtension { public function Test() { return $this->getOwner(); } }'),
            ]*/
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
        $dynamicStaticMethodReturnTypeExtensions = [];
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
}
