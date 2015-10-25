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

class MagnifyTest extends MagnifyTestCase
{
    public static function services()
    {
        return [
            ['normalizer', Normalizer::class],
            ['drivers', DriverRegistry::class],
        ];
    }

    /**
     * @dataProvider services
     */
    public function testCorrectServicesAreRegisteredWithTheApplication($service, $class)
    {
        $m = new Magnify();
        $this->assertArrayHasKey($service, $m);
        $this->assertInstanceOf($class, $m[$service]);
    }

    public function testDriversCanBeRegisteredAndUnregisteryFromMagnify()
    {
        $m = new Magnify();
        $driver = $this->getMock(Driver::class);

        $m->registerDriver($driver);
        $this->assertTrue($m['drivers']->has($driver));

        $m->unregisterDriver($driver);
        $this->assertFalse($m['drivers']->has($driver));
    }
}
