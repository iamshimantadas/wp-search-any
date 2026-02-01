<?php
/**
 * Plugin admin class file
 * 
 * Manages all submenu pages related to plugin
 *
 * @since 1.0.0
 * @package WP_Search_Any
 */
 
if (!defined('ABSPATH')) {
    exit;
}

class WP_Search_Any_Admin {

    /**
     * Constructor
     *
     * @since 1.0.0
     * @return void
     */
    public function __construct() {
        require_once WP_SEARCH_ANY_PATH . 'includes/class-wp-search-any-history.php';
        require_once WP_SEARCH_ANY_PATH . 'includes/class-wp-search-any-settings.php';

        add_action('admin_menu', [$this, 'mc_register_admin_menu']);
        add_action('admin_init', [$this, 'mc_register_settings']);
    }

    /**
     * Register admin menu pages
     *
     * @since 1.0.0
     */
    public function mc_register_admin_menu() {
        add_menu_page(
            __('WP Search Any', 'wp-search-any'),
            __('WP Search Any', 'wp-search-any'),
            'manage_options',
            'wp-search-any',
            [$this, 'mc_render_history_page'],
            'dashicons-search',
            26
        );

        add_submenu_page(
            'wp-search-any',
            __('Search History', 'wp-search-any'),
            __('Search History', 'wp-search-any'),
            'manage_options',
            'wp-search-any',
            [$this, 'mc_render_history_page']
        );

        add_submenu_page(
            'wp-search-any',
            __('Settings', 'wp-search-any'),
            __('Settings', 'wp-search-any'),
            'manage_options',
            'wp-search-any-settings',
            [$this, 'mc_render_settings_page']
        );
    }

    /**
     * Register plugin settings
     *
     * @since 1.0.0
     */
    public function mc_register_settings() {
        register_setting(
            'mc_wp_search_any_settings_group',
            'mc_wp_search_any_settings',
            [$this, 'mc_sanitize_settings']
        );

        add_settings_section(
            'mc_search_main_section',
            __('Search Settings', 'wp-search-any'),
            '__return_false',
            'wp-search-any-settings'
        );

        add_settings_field(
            'enable_ajax_search',
            __('Enable AJAX Search', 'wp-search-any'),
            [$this, 'mc_render_ajax_checkbox'],
            'wp-search-any-settings',
            'mc_search_main_section'
        );
    }

    /**
     * Sanitize settings input
     *
     * @param array $input
     * @return array
     */
    public function mc_sanitize_settings($input) {
        return [
            'enable_ajax_search' => isset($input['enable_ajax_search']) ? 1 : 0,
        ];
    }

    /**
     * Render AJAX checkbox field
     */
    public function mc_render_ajax_checkbox() {
        $options = get_option('mc_wp_search_any_settings', []);
        $ajax_enabled = !empty($options['enable_ajax_search']);
        ?>
        <label>
            <input type="checkbox"
                   name="mc_wp_search_any_settings[enable_ajax_search]"
                   value="1"
                   <?php checked($ajax_enabled); ?>>
            <?php esc_html_e('Enable live AJAX search results', 'wp-search-any'); ?>
        </label>
        <?php
    }

    /**
     * Renders history sub-menu page of the plugin
     *
     * @since 1.0.0
     * @return void
     */
    public function mc_render_history_page() {
        WP_Search_Any_History::render();
    }

    /**
     * Renders settings sub-menu page of the plugin
     *
     * @since 1.0.0
     * @return void
     */
    public function mc_render_settings_page() {
        WP_Search_Any_Settings::render();
    }
}