<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests\Reflection;

use SilbinaryWolf\SilverstripePHPStan\ClassHelper;
use SilbinaryWolf\SilverstripePHPStan\Reflection\MethodClassReflectionExtension;
use SilbinaryWolf\SilverstripePHPStan\Type\DataListType;
use PHPStan\Type\VerbosityLevel;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;

final class SiteTreeMethodClassReflectionExtensionTest extends \PHPStan\Testing\TestCase
{
    /** @var \PHPStan\Broker\Broker */
    private $broker;

    /** @var MethodClassReflectionExtension */
    private $method;

    protected function setUp(): void
    {
        $this->broker = $this->createBroker();
        $this->method = new MethodClassReflectionExtension();
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
        $resultType = $methodReflection->getReturnType();
        self::assertSame(ObjectType::class, get_class($resultType));
        if (!($resultType instanceof ObjectType)) {
            // This statement is needed so PHPStan knows $resultType is ObjectType.
            return;
        }
        self::assertSame(ClassHelper::SiteTree, $resultType->getClassName());
    }

    public function testLinkTrackingMethod(): void
    {
        $classReflection = $this->broker->getClass(ClassHelper::SiteTree);
        $methodReflection = $this->method->getMethod($classReflection, 'LinkTracking');
        self::assertSame('LinkTracking', $methodReflection->getName());
        $dataListType = $methodReflection->getReturnType();
        self::assertSame(DataListType::class, get_class($dataListType));
        if (!($dataListType instanceof DataListType)) {
            // This statement is needed so PHPStan knows $dataListType is DataListType.
            return;
        }
        self::assertSame(ClassHelper::ManyManyList, $dataListType->getClassName());
        $resultType = $dataListType->getItemType();
        self::assertSame(ObjectType::class, get_class($resultType));
        if (!($resultType instanceof ObjectType)) {
            // This statement is needed so PHPStan knows $resultType is ObjectType.
            return;
        }
        self::assertSame(
            $resultType->getClassName(),
            ClassHelper::SiteTree
        );
    }
}
