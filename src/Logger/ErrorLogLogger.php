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

use Psr\Log\LogLevel;

/**
 * A logger implementation that calls `error_log`
 *
 * @since   1.0
 */
final class ErrorLogLogger extends AbstractLogger
{
    private static $levels = array(
        LogLevel::EMERGENCY => 800,
        LogLevel::ALERT     => 700,
        LogLevel::CRITICAL  => 600,
        LogLevel::ERROR     => 500,
        LogLevel::WARNING   => 400,
        LogLevel::NOTICE    => 300,
        LogLevel::INFO      => 200,
        LogLevel::DEBUG     => 100,
    );

    private $level;

    public function __construct($level)
    {
        if (!isset(self::$levels[$level])) {
            $level = LogLEvel::WARNING;
        }

        $this->level = self::$levels[$level];
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $ctx=array())
    {
        if (self::integerLevelFor($level) < $this->level) {
            return;
        }

        error_log(sprintf(
            '[%s] %s',
            $level,
            $this->formatMessage($message, $ctx)
        ));
    }

    private static function integerLevelFor($level)
    {
        return isset(self::$levels[$level]) ? self::$levels[$level] : 1000;
    }
}
