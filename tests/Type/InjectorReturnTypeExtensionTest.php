<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Tests\Type;

use Symbiote\SilverstripePHPStan\Type\InjectorReturnTypeExtension;
use Symbiote\SilverstripePHPStan\Type\SingletonReturnTypeExtension;
use Symbiote\SilverstripePHPStan\ClassHelper;
use Symbiote\SilverstripePHPStan\Tests\ResolverTest;

class InjectorReturnTypeExtensionTest extends ResolverTest
{
    public function dataDynamicMethodReturnTypeExtensions(): array
    {
        return [
            // Injector::inst()->get(ClassHelper::File)
            [
                sprintf(ClassHelper::File),
                sprintf('%s::inst()->get(%s::class)', ClassHelper::Injector, ClassHelper::File),
            ],
            // Test `Injector::inst()->get('Cookie_Backend)` returns `CookieJar` (uses direct value in YML, SS 3.6.X)
            [
                sprintf('%s', ClassHelper::CookieJar),
                sprintf('%s::inst()->get(%s::class)', ClassHelper::Injector, ClassHelper::Cookie_Backend),
            ],
            // Test `Injector::inst()->get("MySQLPDODatabase")` returns `MySQLDatabase` (uses "class" array in YML, SS 3.6.X)
            [
                sprintf('%s', ClassHelper::MySQLDatabase),
                sprintf('%s::inst()->get("MySQLPDODatabase")', ClassHelper::Injector),
            ],
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
    ) {
        $dynamicMethodReturnTypeExtensions = [
            new InjectorReturnTypeExtension(),
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
                sprintf('%s', ClassHelper::File),
                sprintf('singleton("%s")', ClassHelper::File),
            ],
            // Test `singleton("Cookie_Backend")` returns `CookieJar` (uses direct value in YML, SS 3.6.X)
            [
                sprintf('%s', ClassHelper::CookieJar),
                sprintf('singleton("%s")', ClassHelper::Cookie_Backend),
            ],
            // Test `singleton("MySQLPDODatabase")` returns `MySQLDatabase` (uses "class" array in YML, SS 3.6.X)
            [
                sprintf('%s', ClassHelper::MySQLDatabase),
                sprintf('singleton("MySQLPDODatabase")'),
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
    ) {
        $this->assertTypes(
            __DIR__ . '/data/data-object-dynamic-method-return-types.php',
            $description,
            $expression
        );
    }

    public function getDynamicFunctionReturnTypeExtensions(): array
    {
        return [
            new SingletonReturnTypeExtension(),
        ];
    }
}
