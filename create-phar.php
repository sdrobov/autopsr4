<?php

ini_set('phar.readonly', 0);

$pharFile = 'autopsr4.phar';

if (file_exists($pharFile)) {
    unlink($pharFile);
}

if (is_dir(__DIR__ . '/phar')) {
    system('rm -rf ' . __DIR__ . '/phar');
}

mkdir(__DIR__ . '/phar');
copy(__DIR__ . '/autopsr4.php', __DIR__ . '/phar/autopsr4.php');
system('sed -i \'1d\' ' . __DIR__ . '/phar/autopsr4.php');
system('cp -R ' . __DIR__ . '/src ' . __DIR__ . '/phar/src');
system('cp -R ' . __DIR__ . '/vendor ' . __DIR__ . '/phar/vendor');

$p = new Phar($pharFile);
$p->buildFromDirectory(__DIR__ . '/phar');
$p->setStub("#!/usr/bin/php \n" . $p->createDefaultStub('autopsr4.php'));

system('chmod +x ' . $pharFile);
system('rm -rf ' . __DIR__ . '/phar');
