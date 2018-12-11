<?php

namespace AutoPsr4Test;

use AutoPsr4\Parser;
use AutoPsr4\Replacer;
use AutoPsr4\UsageFinder;
use PHPUnit\Framework\TestCase;

class ReplacerTest extends TestCase
{
    const TEST_ROOT_NAMESPACE = 'TestNs';

    public function testReplace()
    {
        chdir(__DIR__ . '/data');

        $parser = new Parser('legacy', static::TEST_ROOT_NAMESPACE);
        $parser->parse();

        $usageFinder = new UsageFinder($parser->getFiles(), $parser->getClasses());
        $usageFinder->findUsages();

        $replacer = new Replacer($usageFinder->getFiles());
        $replacer->replace(true);

        foreach ($replacer->getFiles() as $file) {
            $legacyPath = $file->getPath();
            $psr4Path = str_replace('legacy', 'psr4', $legacyPath);

            $psr4File = file_get_contents($psr4Path);

            $this->assertEquals($psr4File, $file->getContent());
        }
    }
}
