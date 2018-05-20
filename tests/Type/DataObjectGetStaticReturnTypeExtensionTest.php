<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests\Type;

use SilbinaryWolf\SilverstripePHPStan\Type\DataObjectGetStaticReturnTypeExtension;
use SilbinaryWolf\SilverstripePHPStan\ClassHelper;
use SilbinaryWolf\SilverstripePHPStan\Tests\ResolverTest;

class DataObjectGetStaticReturnTypeExtensionTest extends ResolverTest
{
    public function dataDynamicMethodReturnTypeExtensions(): array
    {
        return [
            // Test `SiteTree::get()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
                sprintf('%s::get()', ClassHelper::SiteTree),
            ],
            // DataObject::get('SiteTree')
            [
                sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
                sprintf('%s::get("%s")', ClassHelper::DataObject, ClassHelper::SiteTree),
            ],
            // DataObject::get(ClassHelper::SiteTree)
            [
                sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
                sprintf('%s::get(%s::class)', ClassHelper::DataObject, ClassHelper::SiteTree),
            ],
            // SiteTree::get_one()
            [
                ClassHelper::SiteTree,
                sprintf('%s::get_one()', ClassHelper::SiteTree),
            ],
            // DataObject::get_one('SiteTree')
            [
                ClassHelper::SiteTree,
                sprintf('%s::get_one("%s")', ClassHelper::DataObject, ClassHelper::SiteTree),
            ],
            // DataObject::get_one(ClassHelper::SiteTree)
            [
                sprintf(ClassHelper::SiteTree),
                sprintf('%s::get_one(%s::class)', ClassHelper::DataObject, ClassHelper::SiteTree),
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
            new DataObjectGetStaticReturnTypeExtension(),
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
