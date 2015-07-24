<?php
/**
 * This file is part of the Registers package.
 *
 * (c) Jiří Dušek (http://www.jironett.cz)
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */

if (! $loader = @include __DIR__ . '/../../../autoload.php'){
    echo 'Install packages using `composer install`';
    exit(1);
}

define('TEMP_DIR', __DIR__ . '/tmp/'. (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
@mkdir(dirname(TEMP_DIR));
Tester\Helpers::purge(TEMP_DIR);

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');
Tester\Helpers::purge(TEMP_DIR);

$loader = new Nette\Loaders\RobotLoader;
$loader->addDirectory(TEMP_DIR);
$loader->addDirectory(__DIR__ . '/../src');
$loader->addDirectory(__DIR__ . '/Tests');
$loader->setCacheStorage(new Nette\Caching\Storages\FileStorage(TEMP_DIR));
$loader->register();

function test(Tester\TestCase $testCase)
{
    $testCase->run();
}





