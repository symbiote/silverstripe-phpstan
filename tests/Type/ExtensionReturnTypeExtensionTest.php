<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Tests\Type;

use Symbiote\SilverstripePHPStan\Type\ExtensionReturnTypeExtension;
use Symbiote\SilverstripePHPStan\ConfigHelper;
use Symbiote\SilverstripePHPStan\Tests\ResolverTest;

class ExtensionReturnTypeExtensionTest extends ResolverTest
{
    public function dataDynamicMethodReturnTypeExtensions(): array
    {
        return [
            //  Test `$this->getOwner()` returns `Foo`
            [
                sprintf(\DataExtensionDynamicMethodReturnTypesNamespace\Foo::class),
                sprintf('$this->getOwner()'),
            ],
        ];
    }

    /**
     * Test that owner returns a UnionType if multiple classes are using an extension.
     *
     * @dataProvider dataDynamicMethodReturnTypeExtensions
     * @param string $description
     * @param string $expression
     */
    public function testDynamicMethodReturnTypeExtensions(
        string $description,
        string $expression
    ) {
        $dynamicMethodReturnTypeExtensions = [
            new ExtensionReturnTypeExtension(),
        ];
        $dynamicStaticMethodReturnTypeExtensions = [];
        ConfigHelper::update(
            \DataExtensionDynamicMethodReturnTypesNamespace\Foo::class,
            'extensions',
            [
            \DataExtensionDynamicMethodReturnTypesNamespace\FooDataExtension::class,
            ]
        );
        $this->assertTypes(
            __DIR__ . '/data/data-extension-dynamic-method-return-types.php',
            $description,
            $expression,
            $dynamicMethodReturnTypeExtensions,
            $dynamicStaticMethodReturnTypeExtensions
        );
    }

    public function dataUnionDynamicMethodReturnTypeExtensions(): array
    {
        return [
            //  Test `$this->getOwner()` returns `Foo`
            [
                sprintf(
                    '%s|%s',
                    \DataExtensionUnionDynamicMethodReturnTypesNamespace\Foo::class,
                    \DataExtensionUnionDynamicMethodReturnTypesNamespace\FooTwo::class
                ),
                sprintf('$this->getOwner()'),
            ],
        ];
    }

    /**
     * Test that owner returns a UnionType if multiple classes are using an extension.
     *
     * @dataProvider dataUnionDynamicMethodReturnTypeExtensions
     * @param string $description
     * @param string $expression
     */
    public function testUnionDynamicMethodReturnTypeExtensions(
        string $description,
        string $expression
    ) {
        $dynamicMethodReturnTypeExtensions = [
            new ExtensionReturnTypeExtension(),
        ];
        $dynamicStaticMethodReturnTypeExtensions = [];

        $extensions = [
            \DataExtensionUnionDynamicMethodReturnTypesNamespace\FooDataExtension::class,
        ];
        ConfigHelper::update(
            \DataExtensionUnionDynamicMethodReturnTypesNamespace\Foo::class,
            'extensions',
            $extensions
        );
        ConfigHelper::update(
            \DataExtensionUnionDynamicMethodReturnTypesNamespace\FooTwo::class,
            'extensions',
            $extensions
        );
        $this->assertTypes(
            __DIR__ . '/data/data-extension-union-dynamic-method-return-types.php',
            $description,
            $expression,
            $dynamicMethodReturnTypeExtensions,
            $dynamicStaticMethodReturnTypeExtensions
        );
    }
}
