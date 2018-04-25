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

final class DataObjectGetStaticReturnTypeExtensionTest extends ResolverTest
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
}
