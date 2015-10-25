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
 * Container for drivers. This will keep track of which drivers are registered
 * for the application.
 *
 * @since   1.0
 */
final class DriverRegistry implements \IteratorAggregate
{
    /**
     * @var Driver[]
     */
    private $drivers = [];

    /**
     * Add a new driver to the registry.
     *
     * @param   $driver The driver to add
     * @return  void
     */
    public function add(Driver $driver)
    {
        $this->drivers[get_class($driver)] = $driver;
    }

    /**
     * Remove a driver from the registry.
     *
     * @param   $driver the driver to remove
     * @return  void
     */
    public function remove(Driver $driver)
    {
        unset($this->drivers[get_class($driver)]);
    }

    /**
     * Check to see if a driver is in the registry.
     *
     * @param   $driver The driver to check
     * @return  boolean
     */
    public function has(Driver $driver)
    {
        return isset($this->drivers[get_class($driver)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->drivers);
    }
}
