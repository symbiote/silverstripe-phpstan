<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests;

use PHPStan\Type\VerbosityLevel;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use SiteTree;
use ManyManyList;

final class SiteTreeMethodClassReflectionExtensionTest extends \PHPStan\Testing\TestCase
{
    /** @var \PHPStan\Broker\Broker */
    private $broker;

    /** @var \SilbinaryWolf\SilverstripePHPStan\MethodClassReflectionExtension */
    private $method;

    /** @var \SilbinaryWolf\SilverstripePHPStan\PropertyClassReflectionExtension */
    private $property;

    protected function setUp(): void
    {
        $this->broker = $this->createBroker();
        $this->method = new \SilbinaryWolf\SilverstripePHPStan\MethodClassReflectionExtension();
        $this->method->setBroker($this->broker);
        $this->property = new \SilbinaryWolf\SilverstripePHPStan\PropertyClassReflectionExtension();
    }

    public function dataHasMethod(): array
    {
        return [
            [
                SiteTree::class,
                'Parent',
                true,
            ],
            [
                SiteTree::class,
                'LinkTracking',
                true,
            ],
            [
                SiteTree::class,
                'UnusedMethod',
                false,
            ],
        ];
    }

    /**
     * @dataProvider dataHasMethod
     * @param string $className
     * @param string $method
     * @param bool $result
     */
    public function testHasMethod(string $className, string $method, bool $result): void
    {
        $classReflection = $this->broker->getClass($className);
        self::assertSame($result, $this->method->hasMethod($classReflection, $method));
    }

    public function testParentMethod(): void
    {
        $classReflection = $this->broker->getClass(SiteTree::class);
        $methodReflection = $this->method->getMethod($classReflection, 'Parent');
        self::assertSame('Parent', $methodReflection->getName());
        self::assertSame(SiteTree::class, $methodReflection->getReturnType()->getClassName());
    }

    public function testLinkTrackingMethod(): void
    {
        $classReflection = $this->broker->getClass(SiteTree::class);
        $methodReflection = $this->method->getMethod($classReflection, 'LinkTracking');
        self::assertSame('LinkTracking', $methodReflection->getName());
        $dataListType = $methodReflection->getReturnType();
        self::assertSame(ManyManyList::class, $dataListType->getClassName());
        self::assertSame(SiteTree::class, $dataListType->getItemType()->getClassName());
    }

    /**
     * @return mixed[]
     */
    public function dataHasProperty(): array
    {
        $data = [];
        $data[] = [
            SiteTree::class,
            'ParentID',
            true,
        ];
        $data[] = [
            SiteTree::class,
            'UnusedVariable',
            false,
        ];
        return $data;
    }

    /**
     * @dataProvider dataHasProperty
     * @param string $className
     * @param string $property
     * @param bool $result
     */
    public function testHasProperty(string $className, string $property, bool $result): void
    {
        $classReflection = $this->broker->getClass($className);
        self::assertSame($result, $this->property->hasProperty($classReflection, $property));
    }

    public function testParentIDProperty(): void
    {
        $classReflection = $this->broker->getClass(SiteTree::class);
        $propertyReflection = $this->property->getProperty($classReflection, 'ParentID');
        self::assertSame(IntegerType::class, get_class($propertyReflection->getType()));
    }

    public function testUnusedVariableProperty(): void
    {
        $classReflection = $this->broker->getClass(SiteTree::class);
        $propertyReflection = $this->property->getProperty($classReflection, 'UnusedVariable');
        self::assertSame(NullType::class, get_class($propertyReflection->getType()));
    }
}
