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
 * Marks an object as being as hooking into WordPress somehow. Usually this means
 * that we want a single instance per request.
 *
 * @since   2015-11-15
 */
interface Hookable
{
    /**
     * Call `add_{action,filter}` and connect the object to WordPress.
     *
     * @return void
     */
    public function connect();

    /**
     * Revert any hooks setup in `connect` above.
     *
     * @return void
     */
    public function disconnect();
}
