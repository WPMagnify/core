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

use Magnify\Core\Magnify;
use Magnify\Core\Hookable;

/**
 * Registers (and displays) a new admin page for WP Magnify. This page is will
 * take care of enabling/disabling drivers as well as provide a hoom for driver
 * implementations to place their own options.
 *
 * @since 1.0
 */
final class AdminPage implements Hookable
{
    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        add_action('admin_menu', [$this, 'registerPage']);
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        remove_action('admin_menu', [$this, 'registerPage']);
    }

    public function registerPage()
    {
        $page = add_menu_page(
            __('WP Magnify Settings', MAGNIFY_CORE_TD),
            __('WP Magnify', MAGNIFY_CORE_TD),
            magnify_filter('settings_page_capability', 'manage_options'),
            Magnify::ADMIN_PAGE,
            [$this, 'showPage'],
            null
        );

        add_action("load-{$page}", [$this, 'loadPage']);

        magnify_act('registered_admin_page', $page);
    }

    public function loadPage()
    {
        magnify_act('load_admin_page');
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue()
    {
        magnify_act('admin_enqueue_scripts');
    }

    public function showPage()
    {
        ?>
        <div class="wrap">
            <h1><?php _e('WP Magnify Settings', MAGNIFY_CORE_TD); ?></h1>
            <form method="POST" action="<?php echo admin_url('options.php'); ?>">
                <?php
                settings_fields(Magnify::OPTION_GROUP);
                do_settings_sections(Magnify::OPTION_GROUP);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
