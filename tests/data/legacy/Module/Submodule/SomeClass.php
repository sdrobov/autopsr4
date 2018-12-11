<?php

class Module_Submodule_SomeClass
{
    public function __construct()
    {
        $class1 = new AnotherModule_AnotherClass();
        $class2 = new LegacyClass();
    }
}
