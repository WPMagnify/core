<?php
/*
 * This file is part of the wp-magnify/core package.
 *
 * (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Magnify\Core\Logger;

/**
 * Collections messages in an array. Useful for testing.
 *
 * @since   1.0
 */
final class SpyLogger extends AbstractLogger implements \IteratorAggregate, \Countable
{
    private $messages = [];

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $ctx=[])
    {
        $this->messages[] = sprintf('[%s] %s', $level, $this->formatMessage($message, $ctx));
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->messages);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->messages);
    }
}
