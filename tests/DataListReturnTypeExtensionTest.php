<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests;

// SilverStripe
use DataObject;
use DataList;
use SiteTree;

class DataListReturnTypeExtensionTest extends ResolverTest
{
    public function dataDynamicMethodReturnTypeExtensions(): array
    {
        return [
            // Test `$siteTreeDataList->filter()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->filter(array("ID" => "1"))'),
            ],
            // Test `$siteTreeDataList->filterAny()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->filterAny(array("ID" => "1"))'),
            ],
            // Test `$siteTreeDataList->reverse()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->reverse()'),
            ],
            // Test `SiteTree::get()->where()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->where("1 = 1")'),
            ],
            // Test `$siteTreeDataList->whereAny()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->whereAny("1 = 1")'),
            ],
            // Test `$siteTreeDataList->innerJoin()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->innerJoin("1 = 1", "TableName")'),
            ],
            // Test `$siteTreeDataList->sort()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->sort(array("ID", "Title"))'),
            ],
            // Test `$siteTreeDataList->limit()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->limit(10)'),
            ],
            // Test `$siteTreeDataList->exclude()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->exclude(array("ID" => "1"))'),
            ],
            // Test `$siteTreeDataList->setDataQueryParam()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->setDataQueryParam("Versioned.mode", "all_versions")'),
            ],
            // Test `$siteTreeDataList->alterDataQuery()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->alterDataQuery(function($query){ $query->reverseSort(); })'),
            ],
            // Test `$siteTreeDataList->setQueriedColumns()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->setQueriedColumns(array("ID"))'),
            ],
            // Test `$siteTreeDataList->byIDs()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->byIDs(array(3,5))'),
            ],
            // Test `$siteTreeDataList->addMany()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->addMany(array(1,2))'),
            ],
            // Test `$siteTreeDataList->removeMany()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->removeMany(array(1,2))'),
            ],
            // Test `$siteTreeDataList->removeByFilter()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->removeByFilter(\'"ID" = 1\')'),
            ],
            // Test `$siteTreeDataList->removeAll()` returns `DataList<SiteTree>`
            [
                sprintf('%s<%s>', DataList::class, SiteTree::class),
                sprintf('$siteTreeDataList->removeAll()'),
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
            new \SilbinaryWolf\SilverstripePHPStan\DataListReturnTypeExtension(),
        ];
        $dynamicStaticMethodReturnTypeExtensions = [
            new \SilbinaryWolf\SilverstripePHPStan\DataObjectGetStaticReturnTypeExtension(),
        ];
        $this->assertTypes(
            __DIR__ . '/data/data-list-return-types.php',
            $description,
            $expression,
            $dynamicMethodReturnTypeExtensions,
            $dynamicStaticMethodReturnTypeExtensions
        );
    }
}
