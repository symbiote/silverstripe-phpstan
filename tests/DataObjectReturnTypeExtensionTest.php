<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests;

use SilbinaryWolf\SilverstripePHPStan\ClassHelper;

class DataObjectReturnTypeExtensionTest extends ResolverTest
{
    public function dataDynamicMethodReturnTypeExtensions(): array
    {
        return [
            // Test `$sitetree->dbObject("ID")` returns `DBInt`
            [
                sprintf('%s', ClassHelper::DBInt),
                sprintf('$sitetree->dbObject("%s")', 'ID'),
            ],
            // Test `$sitetree->dbObject("Content")` returns `HTMLText`
            [
                sprintf('%s', ClassHelper::HTMLText),
                sprintf('$sitetree->dbObject("%s")', 'Content'),
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
            new \SilbinaryWolf\SilverstripePHPStan\DataObjectReturnTypeExtension(),
        ];
        $dynamicStaticMethodReturnTypeExtensions = [
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
