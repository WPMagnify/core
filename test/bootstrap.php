<?php
/*
 * This file is part of the wp-magnify/core package.
 *
 * (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


$testDir = getenv('WP_TESTS_DIR') ?: '/tmp/wordpress-tests-lib';
require_once $testDir.'/includes/functions.php';

tests_add_filter('muplugins_loaded', function () {
    require __DIR__.'/../magnify-core.php';
});

require $testDir.'/includes/bootstrap.php';
require_once __DIR__.'/MagnifyTestCase.php';
