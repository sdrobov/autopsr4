<?php

namespace AutoPsr4Test;

use AutoPsr4\ClassEntity;
use AutoPsr4\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    const TEST_ROOT_NAMESPACE = 'TestNs';
    const TEST_NAMESPACE = 'TestNs\\AnotherModule';
    const TEST_OLD_CLASS_NAME = 'AnotherModule_AnotherClass';
    const TEST_NEW_CLASS_NAME = 'AnotherClass';
    const TEST_FQN = 'TestNs\\AnotherModule\\AnotherClass';

    public function testParse()
    {
        chdir(__DIR__ . '/data');

        $file = new File('legacy/AnotherModule/AnotherClass.php');

        $this->assertTrue($file->parse(static::TEST_ROOT_NAMESPACE));

        $class = $file->getClass();
        $this->assertInstanceOf(ClassEntity::class, $class);
        $this->assertEquals(static::TEST_NAMESPACE, $class->getNs());
        $this->assertEquals(static::TEST_OLD_CLASS_NAME, $class->getOldClassName());
        $this->assertEquals(static::TEST_NEW_CLASS_NAME, $class->getShortClassName());
        $this->assertEquals(static::TEST_FQN, $class->getFqn());
    }

    public function testAddUsage()
    {
        chdir(__DIR__ . '/data');

        $file = new File('legacy/AnotherModule/AnotherClass.php');
        $usage1 = new File('legacy/Module/LegacyClass.php');
        $usage2 = new File('legacy/LegacyClass.php');

        $this->assertTrue($file->parse(static::TEST_ROOT_NAMESPACE));
        $this->assertTrue($usage1->parse(static::TEST_ROOT_NAMESPACE));
        $this->assertTrue($usage2->parse(static::TEST_ROOT_NAMESPACE));

        $file->addUsage($usage1->getClass());
        $file->addUsage($usage2->getClass());

        $this->assertNotEmpty($file->getUsages());

        list($usageClass1, $usageClass2) = $file->getUsages();

        $this->assertEquals($usageClass1->getShortClassName(), $usageClass2->getShortClassName());

        $this->assertNotEquals($usageClass1->getAlias(), $usageClass2->getAlias());
    }
}
