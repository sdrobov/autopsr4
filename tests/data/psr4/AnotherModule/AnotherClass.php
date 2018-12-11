<?php


namespace TestNs\AnotherModule;


use TestNs\LegacyClass;
use TestNs\Module\LegacyClass as ModuleLegacyClass;
use TestNs\Module\Submodule\SomeClass;

class AnotherClass extends ModuleLegacyClass
{
    public function __construct()
    {
        $class1 = new LegacyClass();
        $class2 = new SomeClass();
    }
}
