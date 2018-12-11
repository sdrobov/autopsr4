<?php


namespace TestNs\Module;


use TestNs\LegacyClass as LegacyClass1;

class LegacyClass
{
    public function __construct()
    {
        $class1 = new LegacyClass1();
    }
}
