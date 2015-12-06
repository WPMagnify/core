<?php
/*
 * This file is part of the wp-magnify/core package.
 *
 * (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Magnify\Core\Admin;

use Magnify\Core\DriverRegistry;
use Magnify\Core\Hookable;
use Magnify\Core\Magnify;

/**
 * Register the driver settings page and fields.
 *
 * @since 1.0
 */
final class DriverSettings implements Hookable
{
    const SECTION = 'drivers';

    /**
     * @var DriverRegistry
     */
    private $drivers;

    public function __construct(DriverRegistry $drivers)
    {
        $this->drivers = $drivers;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function disconnect()
    {
        remove_action('admin_init', [$this, 'registerSettings']);
    }

    public function registerSettings()
    {
        register_setting(Magnify::OPTION_GROUP, Magnify::SETTING_DRIVERS, [$this, 'validate']);
        add_settings_section(self::SECTION, __('Drivers', MAGNIFY_CORE_TD), function () {
            if (count($this->drivers)) {
                _e('Enable or disable content federation drivers.', MAGNIFY_CORE_TD);
            } else {
                _e('Looks like there are no drivers installed!', MAGNIFY_CORE_TD);
                printf(
                    ' <a href="https://github.com/WPMagnify" target="_blank">%s</a>.',
                    __('Find one', MAGNIFY_CORE_TD)
                );
            }
        }, Magnify::OPTION_GROUP);

        if (count($this->drivers)) {
            add_settings_field(
                'magnify_core_drivers',
                __('Drivers', MAGNIFY_CORE_TD),
                [$this, 'field'],
                Magnify::OPTION_GROUP,
                self::SECTION
            );
        }
    }

    public function validate($in)
    {
        $in = (array) $in;
        $out = [];
        foreach ($this->drivers as $driver) {
            if (!empty($in[$driver->getIdentifier()])) {
                $out[$driver->getIdentifier()] = true;
            }
        }

        return $out;
    }

    public function field()
    {
        $enabled = Magnify::getEnabledDrivers();
        foreach ($this->drivers as $driver) {
            printf(
                '<p><label for="%1$s[%2$s]"><input type="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="1" %3$s /> %4$s</label></p>',
                Magnify::SETTING_DRIVERS,
                $driver->getIdentifier(),
                checked(true, isset($enabled[$driver->getIdentifier()]), false),
                $driver
            );
        }
    }
}
