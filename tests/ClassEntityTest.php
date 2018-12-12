<?php

namespace AutoPsr4Test;

use AutoPsr4\ClassEntity;
use PHPUnit\Framework\TestCase;

class ClassEntityTest extends TestCase
{
    const TEST_OLD_CLASSNAME = 'LegacyClass';
    const TEST_NAMESPACE1 = 'TestNs\\SubNs';
    const TEST_NAMESPACE2 = 'TestNs';
    const TEST_SHORT_CLASS_NAME = 'Test';
    const TEST_USED_ALIASES = ['Test1', 'TestNsTest', 'Test2', 'Test10', 'Test300'];

    public function testGenerateAliasSimple()
    {
        $class = new ClassEntity();
        $class->setShortClassName(static::TEST_SHORT_CLASS_NAME);
        $class->setNs(static::TEST_NAMESPACE1);

        $class->generateAlias();

        $this->assertStringContainsString(static::TEST_SHORT_CLASS_NAME, $class->getAlias());
        $this->assertNotEquals(static::TEST_SHORT_CLASS_NAME, $class->getAlias());
    }

    public function testGenerateAliasWithNumeric()
    {
        $class = new ClassEntity();
        $class->setShortClassName(static::TEST_SHORT_CLASS_NAME);
        $class->setNs(static::TEST_NAMESPACE2);

        $class->generateAlias();

        $this->assertEquals(static::TEST_SHORT_CLASS_NAME . '1', $class->getAlias());
    }

    public function testGenerateAliasWithUsedAliases()
    {
        $class = new ClassEntity();
        $class->setShortClassName(static::TEST_SHORT_CLASS_NAME);
        $class->setNs(static::TEST_NAMESPACE1);

        $class->generateAlias(static::TEST_USED_ALIASES);

        $this->assertStringContainsString(static::TEST_SHORT_CLASS_NAME, $class->getAlias());
        $this->assertNotEquals(static::TEST_SHORT_CLASS_NAME, $class->getAlias());
    }

    public function testAddNamespace()
    {
        $content = file_get_contents(__DIR__ . '/data/legacy/LegacyClass.php');

        $class = new ClassEntity();
        $class->setNs(static::TEST_NAMESPACE1);

        $this->assertStringNotContainsString(static::TEST_NAMESPACE1, $content);

        $content = $class->addNamespace($content);

        $this->assertStringContainsString(static::TEST_NAMESPACE1, $content);
    }

    public function testRenameClassname()
    {
        $content = file_get_contents(__DIR__ . '/data/legacy/LegacyClass.php');

        $class = new ClassEntity();
        $class->setShortClassName(static::TEST_SHORT_CLASS_NAME);

        $this->assertStringContainsString('class ' . static::TEST_OLD_CLASSNAME, $content);
        $this->assertStringNotContainsString(static::TEST_SHORT_CLASS_NAME, $content);

        $content = $class->renameClassname($content);

        $this->assertStringNotContainsString('class ' . static::TEST_OLD_CLASSNAME, $content);
        $this->assertStringContainsString(static::TEST_SHORT_CLASS_NAME, $content);
    }

    public function testRenameUsage()
    {
        $content = file_get_contents(__DIR__ . '/data/legacy/AnotherModule/AnotherClass.php');

        $class = new ClassEntity();
        $class->setShortClassName(static::TEST_SHORT_CLASS_NAME);
        $class->setOldClassName(static::TEST_OLD_CLASSNAME);

        $this->assertStringContainsString('new ' . static::TEST_OLD_CLASSNAME, $content);
        $this->assertStringNotContainsString(static::TEST_SHORT_CLASS_NAME, $content);

        $content = $class->renameUsage($content);

        $this->assertStringNotContainsString('new ' . static::TEST_OLD_CLASSNAME, $content);
        $this->assertStringContainsString(static::TEST_SHORT_CLASS_NAME, $content);
    }
}
