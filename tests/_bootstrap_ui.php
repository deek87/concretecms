<?php

use Illuminate\Filesystem\Filesystem;

// Fix for phpstorm + tests run in separate processes
if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', __DIR__ . '/../concrete/vendor/autoload.php');
}

// Define test constants
putenv('CONCRETE5_ENV=travis');
define('DIR_TESTS', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__));
define('DIR_BASE', dirname(DIR_TESTS));
//define('BASE_URL', 'http://concrete5.test');

// Define concrete5 constants
require DIR_BASE . '/concrete/bootstrap/configure.php';

// Include all autoloaders.
require DIR_BASE_CORE . '/bootstrap/autoload.php';

// Reset the configuration environment
$fs = new Filesystem();
if ($fs->isDirectory(DIR_CONFIG_SITE . '/generated_overrides')) {
    $fs->deleteDirectory(DIR_CONFIG_SITE . '/generated_overrides', true);
} else {
    $fs->makeDirectory(DIR_CONFIG_SITE . '/generated_overrides', 0777, true);
}
$fs->put(DIR_CONFIG_SITE . '/generated_overrides/.gitignore', '');
if ($fs->isDirectory(DIR_CONFIG_SITE . '/doctrine')) {
    $fs->deleteDirectory(DIR_CONFIG_SITE . '/doctrine');
}

// Begin concrete5 startup.
$app = require DIR_BASE_CORE . '/bootstrap/start.php';
/* @var Concrete\Core\Application\Application $app */

// Configure error reporting (test more strictly than core settings)
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

// Unset variables, so that PHPUnit won't consider them as global variables.
unset(
    $app,
    $fs,
    $cn
);
