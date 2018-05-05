<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests;

use SilbinaryWolf\SilverstripePHPStan\ClassHelper;

use PHPStan\Type\VerbosityLevel;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;

final class SiteTreeMethodClassReflectionExtensionTest extends \PHPStan\Testing\TestCase
{
    /** @var \PHPStan\Broker\Broker */
    private $broker;

    /** @var \SilbinaryWolf\SilverstripePHPStan\MethodClassReflectionExtension */
    private $method;

    protected function setUp(): void
    {
        $this->broker = $this->createBroker();
        $this->method = new \SilbinaryWolf\SilverstripePHPStan\MethodClassReflectionExtension();
        $this->method->setBroker($this->broker);
    }

    public function dataHasMethod(): array
    {
        return [
            [
                ClassHelper::SiteTree,
                'Parent',
                true,
            ],
            [
                ClassHelper::SiteTree,
                'LinkTracking',
                true,
            ],
            [
                ClassHelper::SiteTree,
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
        $classReflection = $this->broker->getClass(ClassHelper::SiteTree);
        $methodReflection = $this->method->getMethod($classReflection, 'Parent');
        self::assertSame('Parent', $methodReflection->getName());
        self::assertSame(ClassHelper::SiteTree, $methodReflection->getReturnType()->getClassName());
    }

    public function testLinkTrackingMethod(): void
    {
        $classReflection = $this->broker->getClass(ClassHelper::SiteTree);
        $methodReflection = $this->method->getMethod($classReflection, 'LinkTracking');
        self::assertSame('LinkTracking', $methodReflection->getName());
        $dataListType = $methodReflection->getReturnType();
        self::assertSame(ClassHelper::ManyManyList, $dataListType->getClassName());
        self::assertSame(ClassHelper::SiteTree, $dataListType->getItemType()->getClassName());
    }
}
