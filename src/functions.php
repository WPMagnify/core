<?php
/*
 * This file is part of the wp-magnify/core package.
 *
 * (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Magnify\Core\Admin;
use Magnify\Core\Driver;

/**
 * Fetches the default instance of the `Magnify` class.
 *
 * @return  Magnify\Core\Magnify
 */
function magnify()
{
    return \Magnify::getInstance();
}

/**
 * Hooked into `plugins_loaded` to kick things off. Extensions should hook into
 * `magnify_loaded` to extend this plugin.
 *
 * @return  void
 */
function magnify_core_load()
{
    $magnify = magnify();

    add_filter(magnify_hook('driver_enabled'), '_magnify_disable_inactive_drivers', 10, 2);
    if (is_admin()) {
        $magnify->connect(new Admin\AdminPage());
        $magnify->connect(new Admin\DriverSettings($magnify['drivers']));
    }

    do_action('magnify_loaded', $magnify);
}

function magnify_act($hook, ...$args)
{
    do_action(magnify_hook($hook), ...$args);
}

function magnify_filter($hook, ...$args)
{
    return apply_filters(magnify_hook($hook), ...$args);
}

function magnify_hook($hook)
{
    return sprintf('magnify_%s', $hook);
}

function _magnify_disable_inactive_drivers($bool, Driver $driver)
{
    return \Magnify::driverEnabled($driver);
}
