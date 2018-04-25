<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests;

// SilverStripe
use DBField;
use HTMLText;

class DBFieldGetStaticReturnTypeExtensionTest extends ResolverTest
{
    public function dataDynamicMethodReturnTypeExtensions(): array
    {
        return [
            // Test `DBField::create_field("HTMLText")` returns `HTMLText`
            [
                sprintf('%s', HTMLText::class),
                sprintf('%s::create_field("%s")', DBField::class, HTMLText::class),
            ],
            // Test `DBField::create_field(HTMLText::class)` returns `HTMLText`
            [
                sprintf('%s', HTMLText::class),
                sprintf('%s::create_field(%s::class)', DBField::class, HTMLText::class),
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
        $dynamicMethodReturnTypeExtensions = [];
        $dynamicStaticMethodReturnTypeExtensions = [
            new \SilbinaryWolf\SilverstripePHPStan\DBFieldStaticReturnTypeExtension(),
        ];
        $this->assertTypes(
            __DIR__ . '/data/data-object-dynamic-method-return-types.php',
            $description,
            $expression,
            $dynamicMethodReturnTypeExtensions,
            $dynamicStaticMethodReturnTypeExtensions
        );
    }
}
