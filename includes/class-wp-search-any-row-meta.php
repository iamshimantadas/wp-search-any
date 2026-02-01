<?php
/**
 * Handles plugin row meta links and Thickbox modal
 *
 * @since 1.0.0
 * @package WP_Search_Any
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Search_Any_Plugin_Meta {

    /**
     * Constructor - register meta and modal hooks
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_filter('plugin_row_meta', [$this, 'mc_add_view_details_link'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'mc_load_thickbox']);
        add_action('admin_footer', [$this, 'mc_render_details_modal']);
    }

    /**
     * Load Thickbox script only on plugins page
     *
     * @param string $hook Current admin page hook
     * @since 1.0.0
     */
    public function mc_load_thickbox($hook) {
        if ($hook === 'plugins.php') {
            add_thickbox();
        }
    }

    /**
     * Add "View Details" link to plugin row
     *
     * @param array  $links Plugin action links
     * @param string $file  Plugin file basename
     * @return array Modified links
     * @since 1.0.0
     */
    public function mc_add_view_details_link($links, $file) {
        if ($file === plugin_basename(WP_SEARCH_ANY_FILE)) {
            $links[] = sprintf(
                '<a href="%s" class="thickbox" rel="noopener">%s</a>',
                esc_url('#TB_inline?width=600&height=550&inlineId=wp-search-any-details'),
                esc_html__('View details', 'wp-search-any')
            );
        }
        return $links;
    }

    /**
     * Render Thickbox modal content in admin footer
     *
     * @since 1.0.0
     */
    public function mc_render_details_modal() {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'plugins') {
            return;
        }
        ?>
        <div id="wp-search-any-details" style="display:none;">
            <h2><?php esc_html_e('WP Search Any', 'wp-search-any'); ?></h2>

            <p>
                <?php esc_html_e(
                    'WP Search Any enables a global search across your entire website, including posts, pages, and custom post types.',
                    'wp-search-any'
                ); ?>
            </p>

            <h3><?php esc_html_e('Shortcode Usage', 'wp-search-any'); ?></h3>
            <p><?php esc_html_e('Simple search form:', 'wp-search-any'); ?></p>
            <code>[wp_search_any]</code>

        </div>
        <?php
    }
}