<?php

namespace AutoPsr4;


class App
{
    public function run($argv)
    {
        $options = getopt('n:d:h');

        if (!isset($options['n']) || !isset($options['d']) || isset($options['h'])) {
            $this->usage($argv);
        }

        $parser = new Parser($options['d'], $options['n']);
        $parser->parse();

        $usageFinder = new UsageFinder($parser->getFiles(), $parser->getClasses());
        $usageFinder->findUsages();

        $replacer = new Replacer($usageFinder->getFiles());
        $replacer->replace();
    }

    public function usage($argv)
    {
        echo <<<USAGE
Usage: {$argv[0]} <-n root namespace> <-d src dir>
Or: {$argv[0]} -h for this message

USAGE;

        exit(0);
    }
}
