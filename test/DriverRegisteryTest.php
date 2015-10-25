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
    public function testDriversCanBeAddedAndRemovedFromTheRegistry()
    {
        $reg = new DriverRegistry();
        $driver = $this->getMock(Driver::class);

        $this->assertFalse($reg->has($driver));

        $reg->add($driver);
        $this->assertTrue($reg->has($driver));
        $this->assertCount(1, iterator_to_array($reg));

        $reg->remove($driver);
        $this->assertFalse($reg->has($driver));
    }
}
