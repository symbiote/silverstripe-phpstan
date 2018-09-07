<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Tests\Type;

use Symbiote\SilverstripePHPStan\Type\DBFieldStaticReturnTypeExtension;
use Symbiote\SilverstripePHPStan\ClassHelper;
use Symbiote\SilverstripePHPStan\Tests\ResolverTest;

class DBFieldGetStaticReturnTypeExtensionTest extends ResolverTest
{
    public function dataDynamicMethodReturnTypeExtensions(): array
    {
        return [
            // Test `DBField::create_field("HTMLText")` returns `HTMLText`
            [
                sprintf('%s', ClassHelper::HTMLText),
                sprintf('\%s::create_field("%s")', ClassHelper::DBField, ClassHelper::HTMLText),
            ],
            // Test `DBField::create_field(ClassHelper::HTMLText)` returns `HTMLText`
            [
                sprintf('%s', ClassHelper::HTMLText),
                sprintf('\%s::create_field(%s::class)', ClassHelper::DBField, ClassHelper::HTMLText),
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
            new DBFieldStaticReturnTypeExtension(),
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
