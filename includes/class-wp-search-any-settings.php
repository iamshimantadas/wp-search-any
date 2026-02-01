<?php
/**
 * Settings page rendering for WP Search Any plugin
 *
 * @since 1.0.0
 * @package WP_Search_Any
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Search_Any_Settings {

    /**
     * Render the settings page HTML output
     *
     * @since 1.0.0
     * @return void
     */
    public static function render() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('WP Search Any Settings', 'wp-search-any'); ?></h1>

            <form method="post" action="options.php">
                <?php
                // Use the new settings group name (consistent with register_setting in admin class)
                settings_fields('mc_wp_search_any_settings_group');
                
                // Use the new page slug (consistent with add_submenu_page)
                do_settings_sections('wp-search-any-settings');
                
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}