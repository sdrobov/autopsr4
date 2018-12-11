<?php

class AnotherModule_AnotherClass extends Module_LegacyClass
{
    public function __construct()
    {
        $class1 = new LegacyClass();
        $class2 = new Module_Submodule_SomeClass();
    }
}
