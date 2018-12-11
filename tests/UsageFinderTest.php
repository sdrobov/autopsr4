<?php

namespace AutoPsr4Test;

use AutoPsr4\ClassEntity;
use AutoPsr4\File;
use AutoPsr4\UsageFinder;
use PHPUnit\Framework\TestCase;

class UsageFinderTest extends TestCase
{
    const TEST_ROOT_NAMESPACE = 'TestNs';
    const TEST_CLASSES_IN_USE = ['LegacyClass', 'AnotherModule_AnotherClass'];
    const TEST_CLASS_NOT_IN_USE = 'Module_LegacyClass';

    public function testFindUsages()
    {
        chdir(__DIR__ . '/data');

        $file = new File('legacy/Module/Submodule/SomeClass.php');
        $this->assertTrue($file->parse(static::TEST_ROOT_NAMESPACE));

        $classes = array_map(function ($className) {
            return (new ClassEntity())->setOldClassName($className)->setFqn($className);
        }, static::TEST_CLASSES_IN_USE);
        $classes[] = (new ClassEntity())->setOldClassName(static::TEST_CLASS_NOT_IN_USE);

        $usageFinder = new UsageFinder([$file], $classes);
        $usageFinder->findUsages();

        $fileWithUsages = $usageFinder->getFiles()[0];
        $this->assertNotEmpty($fileWithUsages->getUsages());

        $classesInUse = array_map(function (ClassEntity $classEntity) {
            return $classEntity->getOldClassName();
        }, $fileWithUsages->getUsages());

        $this->assertNotContains(static::TEST_CLASS_NOT_IN_USE, $classesInUse);
        $this->assertEquals(static::TEST_CLASSES_IN_USE, $classesInUse);
    }
}
