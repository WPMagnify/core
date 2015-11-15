<?php
/*
 * This file is part of the wp-magnify/core package.
 *
 * (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Magnify\Core;

use Psr\Log\LogLevel;

/**
 * Provides a nice entry point for plugins as well as being the container 
 * for all the services.
 *
 * This also provides a singleton-ish implementation (without the restriction on
 * multiple instances) to play nice with the WordPress ecosystem.
 *
 * @since   1.0
 */
final class Magnify extends \Pimple\Container
{
    const ADMIN_PAGE = 'wp-magnify';
    const OPTION_GROUP = 'wp_magnify';
    const SETTING_DRIVERS = 'wp_magnify_drivers';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct();
        $this['normalizer'] = function () {
            return new Normalizer\FilteringNormalizer(
                new Normalizer\DefaultNormalizer()
            );
        };
        $this['logger'] = function () {
            return new Logger\ErrorLogLogger(WP_DEBUG ? LogLevel::INFO : LogLevel::ERROR);
        };
        $this['drivers'] = function ($magnify) {
            return new DriverRegistry($magnify['normalizer'], $magnify['logger']);
        };
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function registerDriver(Driver $driver)
    {
        $this->offsetGet('drivers')->add($driver);
    }

    public function unregisterDriver(Driver $driver)
    {
        $this->offsetGet('drivers')->remove($driver);
    }

    /**
     * Connect an object to the magnify instance. This will store the object
     * in the the application with the key `get_class($object)` then call `connect`
     * on it.
     *
     * Should you want to disconnect an object later on, you can retrieve it
     * via `$mangify['Fully\\Qualified\\ClassName']->disconnect();`.
     *
     * @return void
     */
    public function connect(Hookable $object)
    {
        // Pimple will call any invokable object when it fetches it from
        // the container. If we get an invokeable object, we'll make sure
        // the container doesn't do that via `protect`.
        $set = $object;
        if (method_exists($object, '__invoke')) {
            $set = $this->protect($object);
        }

        $this->offsetSet(get_class($object), $set);
        $object->connect();
    }

    public static function getEnabledDrivers()
    {
        return get_option(self::SETTING_DRIVERS);
    }

    public static function driverEnabled(Driver $driver)
    {
        $e = self::getEnabledDrivers();
        return !empty($e[$driver->getIdentifier()]);
    }
}
