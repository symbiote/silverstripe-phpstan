<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests;

use SilbinaryWolf\SilverstripePHPStan\ClassHelper;

use PHPStan\Type\VerbosityLevel;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;

final class SiteTreePropertyClassReflectionExtensionTest extends \PHPStan\Testing\TestCase
{
    /** @var \PHPStan\Broker\Broker */
    private $broker;

    /** @var \SilbinaryWolf\SilverstripePHPStan\PropertyClassReflectionExtension */
    private $property;

    protected function setUp(): void
    {
        $this->broker = $this->createBroker();
        $this->property = new \SilbinaryWolf\SilverstripePHPStan\PropertyClassReflectionExtension();
    }

    /**
     * @return mixed[]
     */
    public function dataHasProperty(): array
    {
        $data = [];
        $data[] = [
            ClassHelper::SiteTree,
            'ParentID',
            true,
        ];
        $data[] = [
            ClassHelper::SiteTree,
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
        $classReflection = $this->broker->getClass(ClassHelper::SiteTree);
        $propertyReflection = $this->property->getProperty($classReflection, 'ParentID');
        self::assertSame(IntegerType::class, get_class($propertyReflection->getType()));
    }

    public function testUnusedVariableProperty(): void
    {
        $classReflection = $this->broker->getClass(ClassHelper::SiteTree);
        $propertyReflection = $this->property->getProperty($classReflection, 'UnusedVariable');
        self::assertSame(NullType::class, get_class($propertyReflection->getType()));
    }
}
