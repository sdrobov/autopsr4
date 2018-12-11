<?php


namespace TestNs;


use TestNs\AnotherModule\AnotherClass;
use TestNs\Module\LegacyClass as ModuleLegacyClass;
use TestNs\Module\Submodule\SomeClass;

class LegacyClass
{
    public function __construct()
    {
        $class1 = new ModuleLegacyClass();
        $class2 = new SomeClass();
        $class3 = new AnotherClass();
    }
}
