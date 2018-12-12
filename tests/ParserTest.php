<?php

namespace AutoPsr4Test;

use AutoPsr4\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    const TEST_ROOT_NAMESPACE = 'TestNs';
    const TEST_ROOT_DIR = 'legacy';
    const TEST_FILES = 4;
    const TEST_CLASSES = 4;

    public function testParse()
    {
        chdir(__DIR__ . '/data');

        $parser = new Parser(static::TEST_ROOT_DIR, static::TEST_ROOT_NAMESPACE);
        $parser->parse();

        $this->assertCount(static::TEST_FILES, $parser->getFiles());
        $this->assertCount(static::TEST_CLASSES, $parser->getClasses());
    }
}
