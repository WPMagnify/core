<?php
/*
 * This file is part of the wp-magnify/core package.
 *
 * (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Pimple\Container;

function magnify_core_load()
{
    $container = new Container();

    do_action('magnify_loaded', $container);
}
