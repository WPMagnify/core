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

use Psr\Log\LoggerInterface;

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
     * @var SyncHandler[]
     */
    private $handlers = [];

    /**
     * @var Normalizer
     */
    private $normalizer;

    /**
     * @var LoggerInterface
     */
    private $logger;


    public function __construct(Normalizer $normalizer, LoggerInterface $logger)
    {
        $this->normalizer = $normalizer;
        $this->logger = $logger;
    }

    /**
     * Add a new driver to the registry.
     *
     * @param   $driver The driver to add
     * @return  void
     */
    public function add(Driver $driver)
    {
        $key = $this->keyFor($driver);
        $this->drivers[$key] = $driver;
        $this->handlers[$key] = new SyncHandler($driver, $this->normalizer, $this->logger);
        $this->handlers[$key]->connect();
    }

    /**
     * Remove a driver from the registry.
     *
     * @param   $driver the driver to remove
     * @return  void
     */
    public function remove(Driver $driver)
    {
        $key = $this->keyFor($driver);
        if (isset($this->drivers[$key])) {
            $this->handlers[$key]->disconnect();
            unset($this->drivers[$key], $this->handlers[$key]);
        }
    }

    /**
     * Check to see if a driver is in the registry.
     *
     * @param   $driver The driver to check
     * @return  boolean
     */
    public function has(Driver $driver)
    {
        return isset($this->drivers[$this->keyFor($driver)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->drivers);
    }

    private function keyFor(Driver $driver)
    {
        return get_class($driver);
    }
}
