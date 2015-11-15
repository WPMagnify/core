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

class DriverRegistryTest extends MagnifyTestCase
{
    private $normalier, $logger, $registry;

    public function testDriversCanBeAddedAndRemovedFromTheRegistry()
    {
        $driver = $this->getMock(Driver::class);
        $driver->expects($this->atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('testDriver');

        $this->assertFalse($this->registry->has($driver));

        $this->registry->add($driver);
        $this->assertTrue($this->registry->has($driver));
        $this->assertCount(1, iterator_to_array($this->registry));

        $this->registry->remove($driver);
        $this->assertFalse($this->registry->has($driver));
    }

    public function setUp()
    {
        parent::setUp();
        $this->logger = new Logger\SpyLogger();
        $this->normalizer = $this->getMock(Normalizer::class);
        $this->registry = new DriverRegistry($this->normalizer, $this->logger);
    }
}
