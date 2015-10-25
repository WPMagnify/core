<?php
/*
 * Plugin Name: WP Magnify Core
 * Plugin URI: http://wpmagnify.org
 * Description: A content federation and search plugin for WordPress.
 * Version: 1.0
 * Text Domain: wp-magnify-core
 * Author: Christopher Davis
 * Author URI: http://christopherdavis.me
 * License: MIT
 *
 * This file is part of the wp-magnify/core package.
 *
 * (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

!defined('ABSPATH') && exit;

define('WP_MAGNIFYCORE_TD', 'wp-magnify-core');
define('WP_MAGNIFYCORE_FILE', __FILE__);
define('WP_MAGNIFYCORE_VER', '1.0.0');

if (file_exists(__DIR__.'/vendor/autoload.php')) { // local dev or installed via wp.org
    require __DIR__.'/vendor/autoload.php';
} elseif (!function_exists('magnify_core_load')) { // plugin installed with composer
    return; // TODO show an error here
}

add_action('plugins_loaded', 'magnify_core_load');
