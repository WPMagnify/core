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

abstract class AbstractLogger extends \Psr\Log\AbstractLogger
{
    protected function formatMessage($message, array $ctx)
    {
        return strtr($message, $this->makeReplacements($ctx));
    }

    protected function makeReplacements(array $ctx)
    {
        $out = [];
        foreach ($ctx as $key => $val) {
            $out[sprintf('{%s}', $key)] = $this->isStringy($val) ? (string) $val : json_encode($val);
        }

        return $out;
    }

    protected function isStringy($val)
    {
        return is_scalar($val) || (is_object($val) && method_exists($val, '__toString'));
    }
}
