<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests;

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

// NOTE(Jake): 2018-04-05
//
// Workaround Composer Autoloader / PHPUnit not working as I expect.
//
include_once(dirname(__FILE__).'/ResolverTest.php');

class DataObjectGetStaticReturnTypeExtensionTest extends ResolverTest
{
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
        $dynamicMethodReturnTypeExtensions = [];
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
}
