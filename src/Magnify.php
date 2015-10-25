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
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
