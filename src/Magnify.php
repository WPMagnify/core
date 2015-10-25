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
}
