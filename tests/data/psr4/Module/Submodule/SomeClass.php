<?php


namespace TestNs\Module\Submodule;


use TestNs\AnotherModule\AnotherClass;
use TestNs\LegacyClass;

class SomeClass
{
    public function __construct()
    {
        $class1 = new AnotherClass();
        $class2 = new LegacyClass();
    }
}
