#!/usr/bin/php
<?php

if ($argc != 3) {
    usage();
}

$srcRoot = realpath($argv[1]);
$rootNs = $argv[2];

function usage()
{
    global $argv;

    echo "{$argv[0]} <src root> <root namespace>";

    die;
}

function dirRecursive($dirname)
{
    global $srcRoot;

    $files = [];
    foreach (glob("{$dirname}/*") as $entity) {
        if (preg_match('/\.php$/', $entity)) {
            $entity = str_replace(dirname($srcRoot) . '/', '', $entity);
            $files[] = $entity;

            continue;
        }

        if (is_dir($entity)) {
            $dirContent = dirRecursive("$entity");
            $files = array_merge($files, $dirContent);
        }
    }

    return $files;
}

$files = dirRecursive($srcRoot);
$fileReplaceMap = [];
$classReplaceMap = [];

echo 'Step1: building replace maps' . PHP_EOL;
foreach ($files as $file) {
    echo "Processing {$file}...";

    $parts = explode('/', $file);
    array_shift($parts);

    $possibleClassName = array_pop($parts);
    $possibleClassName = str_replace('.php', '', $possibleClassName);

    $ns = $rootNs . (empty($parts) ? '' : '\\' . implode("\\", $parts));

    $content = file_get_contents($file);

    $match = [];
    if (preg_match('/^namespace ([^;]+);$/m', $content, $match)) {
        if ($match[1] == $ns) {
            echo ' file already has valid namespace, SKIP' . PHP_EOL;

            continue;
        }

        echo " file has namespace {$match[1]}, but it doesnt match {$ns} we guess for it, SKIP" . PHP_EOL;

        continue;
    }

    $match = [];
    if (!preg_match('/^(?:abstract )?(?:class|interface) (\w+)/m', $content, $match)) {
        echo ' class not found, SKIP' . PHP_EOL;

        continue;
    }

    $oldClassName = $match[1];
    $classParts = explode('_', $oldClassName);
    $realClassName = array_pop($classParts);

    if ($realClassName != $possibleClassName) {
        echo " {$realClassName} != {$possibleClassName}, SKIP" . PHP_EOL;

        continue;
    }

    $fqn = "{$ns}\\{$realClassName}";
    echo "; old class name: {$oldClassName}; new FQN: {$fqn};";
    $fileReplaceMap[$file] = [
        'className' => $realClassName,
        'ns' => $ns,
        'fqn' => $fqn,
    ];
    $classReplaceMap[$oldClassName] = $fqn;

    echo ' DONE' . PHP_EOL;
}

echo PHP_EOL . 'Step 2: finding usages' . PHP_EOL;
foreach ($fileReplaceMap as $file => &$replaces) {
    echo "Processing {$file}... ";

    $content = file_get_contents($file);

    $replaces['usages'] = [];
    $replaces['uses'] = [];
    foreach ($classReplaceMap as $old => $new) {
        $parts = explode('\\', $new);
        $shortName = array_pop($parts);
        $ns = implode('\\', $parts);

        if ($new == $replaces['fqn'] || $ns == $replaces['ns']) {
            $replaces['usages'][$old] = $shortName;

            continue;
        }

        $match = [];
        if (
        preg_match_all(
            "/(?<!class )(?<!interface )(?<!namespace )(?<!\$)(?<!\w){$old}(?!\w)/",
            $content,
            $match
        )
        ) {
            if (in_array($shortName, $replaces['usages']) && !array_key_exists($old, $replaces['usages'])) {
                if (count($parts) > 1) {
                    $shortName = end($parts) . $shortName;
                } else {
                    $shortName .= '1';
                }

                $new .= " as {$shortName}";
            }

            $replaces['uses'][] = $new;
            $replaces['usages'][$old] = $shortName;
        }
    }

    echo 'DONE' . PHP_EOL;
}

echo PHP_EOL . 'Step 3: doing replaces' . PHP_EOL;
foreach ($fileReplaceMap as $file => $replaces) {
    echo "Processing {$file}... ";

    $content = file_get_contents($file);

    $content = preg_replace(
        '/\<\?(php)?\n(\n?\/\*(?:[^\/]|\n)+\*\/\n)?/ms',
        "<?php\n$2\n\nnamespace {$replaces['ns']};\n\n",
        $content
    );
    $content = preg_replace(
        '/^(abstract )?(class|interface) \w+/m',
        "$1$2 {$replaces['className']}",
        $content,
        1
    );

    if (!empty($replaces['uses'])) {
        $uses = implode(
            "\n",
            array_map(
                function ($use) {
                    return "use {$use};";
                },
                $replaces['uses']
            )
        );

        $content = preg_replace(
            '/^^(\/\*(?:[^\/]|\n)+\*\/\n)?(abstract )?(class|interface)/m',
            "{$uses}\n\n$1$2$3",
            $content
        );
    }

    foreach ($replaces['usages'] as $old => $new) {
        $content = preg_replace(
            "/(?<!class )(?<!interface )(?<!namespace )(?<!\$)(?<!\\\\)(?<!\w)(?<!')(?<!\"){$old}(?!\w)/",
            $new,
            $content
        );
    }

    file_put_contents($file, $content);

    echo 'DONE' . PHP_EOL;
}
